<?php

namespace App\Services;

use App\Queries\QueryInterface;
use App\Queries\CreateBoardMutation;
use App\Queries\GetPortfolioItemDetailsQuery;
use App\Queries\GetPortfolioItemMirrorLinkDetailsQuery;
use App\Queries\GetMondayUserInfoQuery;
use App\Queries\GetProjectBoardTasksQuery; // Ensure this is correctly imported
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MondayApiService
{
    protected string $apiToken;
    protected string $apiUrl;
    protected string $portfolioBoardId;

    public function __construct(string $apiToken, string $apiUrl, string $portfolioBoardId)
    {
        $this->apiToken = $apiToken;
        $this->apiUrl = $apiUrl;
        $this->portfolioBoardId = $portfolioBoardId;
        // Log::info('MondayApiService initialized with Portfolio Board ID: ' . $this->portfolioBoardId); // Can be commented out
    }

    public function executeQuery(QueryInterface $query): array
    {
        return $this->execute($query->getQuery(), $query->getVariables());
    }

    public function createBoardFromTemplate(string $boardName, string $boardKind, ?int $templateId = null, array $ownerIds = [], ?int $workspaceId = null): array
    {
        $mutation = new CreateBoardMutation($boardName, $boardKind, $templateId, $ownerIds, $workspaceId);
        $response = $this->executeQuery($mutation);
        if (isset($response['data']['create_board'])) {
            return $response['data']['create_board'];
        }
        Log::warning('create_board mutation did not return the expected data structure.', ['response' => $response]);
        throw new \RuntimeException('Failed to create board or parse response correctly. "create_board" key missing in data.');
    }

    public function getPortfolioItemDetails(string $portfolioItemId, array $columnIdsToFetch): ?array
    {
        $columnIdsToFetch = array_filter($columnIdsToFetch, fn($id) => $id !== 'name' && !empty($id));
        $query = new GetPortfolioItemDetailsQuery($portfolioItemId, $this->portfolioBoardId, $columnIdsToFetch);
        try {
            $response = $this->executeQuery($query);
            $items = $response['data']['boards'][0]['items_page']['items'] ?? [];
            if (empty($items)) {
                Log::warning("No item found on portfolio board for item ID: {$portfolioItemId} when fetching general details.");
                return null;
            }
            return $items[0];
        } catch (\Exception $e) {
            Log::error("Error fetching general portfolio item details for ID {$portfolioItemId}: " . $e->getMessage());
            throw $e;
        }
    }

    public function getLinkedBoardIdFromMirrorColumn(string $portfolioItemId, string $targetMirrorColumnId): ?string
    {
        Log::info("MIRROR_FETCH: Attempting for Item ID: {$portfolioItemId}, Target Column ID: '{$targetMirrorColumnId}', on Portfolio Board: {$this->portfolioBoardId}");
        $query = new GetPortfolioItemMirrorLinkDetailsQuery($portfolioItemId, $this->portfolioBoardId);
        try {
            $response = $this->executeQuery($query);
            $items = $response['data']['boards'][0]['items_page']['items'] ?? [];
            if (empty($items)) {
                Log::warning("MIRROR_FETCH: No item object found for item ID: {$portfolioItemId}. API response might be empty for items_page.", ['response_data_items' => $items]);
                return null;
            }
            $item = $items[0];
            $columnValues = $item['column_values'] ?? [];
            if (empty($columnValues)) {
                Log::warning("MIRROR_FETCH: 'column_values' array is empty for item ID: {$portfolioItemId}.", ['item_data' => $item]);
                return null;
            }
            Log::info("MIRROR_FETCH: Iterating column_values for item ID: {$portfolioItemId}. Target Mirror Column ID: '{$targetMirrorColumnId}'. Column count: " . count($columnValues));
            foreach ($columnValues as $cv) {
                $currentColumnId = $cv['id'] ?? null;
                $currentColumnType = $cv['type'] ?? null;
                Log::info("MIRROR_FETCH_LOOP: Checking column with ID: '{$currentColumnId}', Type: '{$currentColumnType}'");
                if ($currentColumnId === $targetMirrorColumnId) {
                    Log::info("MIRROR_FETCH_LOOP: Matched target Mirror Column ID '{$targetMirrorColumnId}'. Column Data: ", $cv);
                    if ($currentColumnType !== 'mirror') {
                        Log::warning("MIRROR_FETCH_LOOP: Column '{$targetMirrorColumnId}' was found, but its type is '{$currentColumnType}', not 'mirror'.", ['column_data' => $cv]);
                        return null;
                    }
                    if (isset($cv['mirrored_items']) && is_array($cv['mirrored_items']) && !empty($cv['mirrored_items'])) {
                        if (isset($cv['mirrored_items'][0]['linked_board_id'])) {
                            $linkedBoardId = (string) $cv['mirrored_items'][0]['linked_board_id'];
                            Log::info("MIRROR_FETCH_SUCCESS: Extracted linked_board_id: '{$linkedBoardId}' from column '{$targetMirrorColumnId}'");
                            return $linkedBoardId;
                        } else {
                            Log::warning("MIRROR_FETCH_FAIL: Mirror column '{$targetMirrorColumnId}' found, 'mirrored_items' array is present and not empty, but 'linked_board_id' is missing in the first element.", ['mirrored_items_data' => $cv['mirrored_items']]);
                            return null;
                        }
                    } else {
                        Log::warning("MIRROR_FETCH_FAIL: Mirror column '{$targetMirrorColumnId}' found, but 'mirrored_items' key is missing, not an array, or is empty.", ['column_data' => $cv]);
                        return null;
                    }
                }
            }
            Log::warning("MIRROR_FETCH_FAIL: Target mirror column '{$targetMirrorColumnId}' was NOT found after looping through all column_values for item {$portfolioItemId}.", ['all_columns_returned_for_mirror_query' => $columnValues]);
            return null;
        } catch (\Exception $e) {
            Log::error("Error fetching linked board ID from mirror column for portfolio item ID {$portfolioItemId}: " . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    public function getMondayUserProfilePhoto(string $mondayUserId): ?string
    {
        if (empty(trim($mondayUserId))) {
            Log::warning('getMondayUserProfilePhoto called with an empty Monday User ID.');
            return null;
        }
        $query = new GetMondayUserInfoQuery([(int)$mondayUserId]);
        try {
            $response = $this->executeQuery($query);
            $users = $response['data']['users'] ?? [];
            if (!empty($users) && isset($users[0]['photo_small'])) {
                return $users[0]['photo_small'] ?: null;
            }
            Log::warning("No photo_small found for Monday User ID: {$mondayUserId} or user not found.", ['response' => $response]);
            return null;
        } catch (\Exception $e) {
            Log::error("Error fetching Monday user photo for ID {$mondayUserId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetches tasks from a specific Project Board that are marked to be shown in the portal.
     *
     * @param string $projectBoardId The ID of the Monday.com Project Board.
     * @param string $showInPortalColumnId The ID of the column used to filter tasks (e.g., "color_mkrh753c").
     * @param array $taskDetailColumnIds An array of column IDs to fetch for each task's details.
     * @param string $showInPortalTextValue The text value indicating task should be shown (e.g., "yes").
     * @param int $limitPerPage Number of items to fetch per API call for pagination.
     * @return array An array of task items that match the criteria.
     * @throws \Exception
     */
    public function getTasksToShowInPortal(
        string $projectBoardId,
        string $showInPortalColumnId,
        array $taskDetailColumnIds = [], // This parameter is needed
        string $showInPortalTextValue = 'yes',
        int $limitPerPage = 100
    ): array {
        Log::info("TASK_FETCH (Paginated): Starting for Project Board ID: {$projectBoardId}, ShowInPortalColumn: {$showInPortalColumnId}, Expected Value: '{$showInPortalTextValue}', Details Columns: " . implode(',', $taskDetailColumnIds));

        $allMatchingTasks = [];
        $cursor = null;
        $pageNumber = 0;
        $maxPages = 10; // Safeguard

        try {
            do {
                $pageNumber++;
                Log::info("TASK_FETCH (Paginated): Fetching page {$pageNumber}, Cursor: " . ($cursor ?? 'none'));

                // *** THIS IS THE LINE (OR AROUND HERE) THAT NEEDS TO BE CORRECT ***
                $query = new GetProjectBoardTasksQuery(
                    $projectBoardId,
                    $showInPortalColumnId,
                    $taskDetailColumnIds, // 3rd argument - $taskDetailColumnIds
                    $limitPerPage,        // 4th argument - $limitPerPage
                    $cursor               // 5th argument - $cursor
                );
                // ******************************************************************

                $response = $this->executeQuery($query);

                $boardData = $response['data']['boards'][0] ?? null;

                if (!$boardData || !isset($boardData['items_page']['items'])) {
                    Log::warning("TASK_FETCH (Paginated): No items or board data found for Project Board ID: {$projectBoardId} on page {$pageNumber}.", ['response_data' => $response['data'] ?? null]);
                    break;
                }

                $itemsOnPage = $boardData['items_page']['items'];
                Log::info("TASK_FETCH (Paginated): Received " . count($itemsOnPage) . " tasks on page {$pageNumber}. Raw items: ", $itemsOnPage);


                if (empty($itemsOnPage)) {
                    break;
                }

                foreach ($itemsOnPage as $task) {
                    $taskId = $task['id'] ?? 'UNKNOWN_ID';
                    $showInPortalColValue = null;
                    // The 'column_values' from GetProjectBoardTasksQuery GQL is for the $showInPortalColumnId
                    // The 'details' from GetProjectBoardTasksQuery GQL is for the $taskDetailColumnIds
                    if (isset($task['column_values'])) {
                        foreach ($task['column_values'] as $cv) {
                            if (isset($cv['id']) && $cv['id'] === $showInPortalColumnId) {
                                $showInPortalColValue = $cv;
                                break;
                            }
                        }
                    }

                    if ($showInPortalColValue && isset($showInPortalColValue['text']) && strtolower(trim($showInPortalColValue['text'])) === strtolower(trim($showInPortalTextValue))) {
                        $taskDataToStore = [
                            'id' => $task['id'],
                            'name' => $task['name'],
                            'column_values' => $task['details'] ?? [], // Use the 'details' alias for other columns
                        ];
                        $allMatchingTasks[] = $taskDataToStore;
                        Log::info("TASK_FETCH_LOOP: Task ID {$taskId} MATCHED and processed.");
                    }
                }

                $cursor = $boardData['items_page']['cursor'] ?? null;
                Log::info("TASK_FETCH (Paginated): Next cursor for page " . ($pageNumber + 1) . ": " . ($cursor ?? 'none'));
            } while ($cursor && $pageNumber < $maxPages);

            if ($pageNumber >= $maxPages && $cursor) {
                Log::warning("TASK_FETCH (Paginated): Reached max page limit ({$maxPages}) for board {$projectBoardId}. There might be more tasks.");
            }

            Log::info("TASK_FETCH (Paginated): Completed. Found " . count($allMatchingTasks) . " total tasks to show in portal for board {$projectBoardId} after processing {$pageNumber} page(s).");
            return $allMatchingTasks;
        } catch (\Exception $e) {
            Log::error("TASK_FETCH (Paginated): Error fetching tasks for Project Board ID {$projectBoardId}: " . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    protected function execute(string $queryString, array $variables = []): array
    {
        $requestBody = [
            'query' => $queryString,
            'variables' => (object) $variables
        ];
        Log::debug('Monday API Request:', ['url' => $this->apiUrl, 'body' => $requestBody]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->apiToken,
            'API-Version' => '2023-10',
        ])->post($this->apiUrl, $requestBody);
        $responseData = $response->json();
        Log::debug('Monday API Response:', ['status' => $response->status(), 'body' => $responseData, 'headers' => $response->headers()]);
        if (!$response->successful()) {
            Log::error('Monday API HTTP Error:', ['status' => $response->status(), 'response_body' => $responseData, 'request_query' => $queryString, 'request_variables' => $variables]);
            throw new \RuntimeException("Monday API HTTP request failed with status {$response->status()}: " . ($response->body() ?: 'No response body'));
        }
        if (isset($responseData['errors']) && !empty($responseData['errors'])) {
            Log::error('Monday API GraphQL Error:', ['errors' => $responseData['errors'], 'request_query' => $queryString, 'request_variables' => $variables]);
            throw new \RuntimeException("Monday API GraphQL error: " . json_encode($responseData['errors']));
        }
        if (!isset($responseData['data']) && empty($responseData['errors'])) {
            Log::warning('Monday API Response missing "data" key and no GraphQL errors reported:', ['response_body' => $responseData, 'request_query' => $queryString, 'request_variables' => $variables]);
        }
        return $responseData;
    }

    public function getColumnDataById(array $allColumnValues, string $targetColumnId): ?array
    {
        foreach ($allColumnValues as $column) {
            if (isset($column['id']) && $column['id'] === $targetColumnId) {
                return $column;
            }
        }
        return null;
    }

    public function getPortfolioBoardId(): string
    {
        return $this->portfolioBoardId;
    }
}
