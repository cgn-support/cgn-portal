<?php

namespace App\Services;

use App\Queries\QueryInterface;
use App\Queries\CreateBoardMutation;
use App\Queries\GetPortfolioItemDetailsQuery;
use App\Queries\GetPortfolioItemMirrorLinkDetailsQuery;
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
        Log::info('MondayApiService initialized with Portfolio Board ID: ' . $this->portfolioBoardId);
    }

    public function executeQuery(QueryInterface $query): array
    {
        return $this->execute($query->getQuery(), $query->getVariables());
    }

    // createBoardFromTemplate method as before...
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

    // getPortfolioItemDetails method as before...
    public function getPortfolioItemDetails(string $portfolioItemId, array $columnIdsToFetch): ?array
    {
        $columnIdsToFetch = array_filter($columnIdsToFetch, fn($id) => $id !== 'name' && !empty($id));
        // It's important that GetPortfolioItemDetailsQuery can handle an empty $columnIdsToFetch array gracefully
        // or that $columnIdsToFetch is never empty when this method is called.
        // The GQL query `get_portfolio_item_details.graphql` requires $columnIds.
        // If $columnIdsToFetch is empty, the GQL variable will be an empty array.
        // Monday API might return an error or empty column_values if `ids: []` is passed for column_values.
        // For safety, ensure $columnIdsToFetch is not empty if your GQL requires it.
        // However, if the goal is just to get item 'id' and 'name', a different, simpler query would be better.
        // For now, assuming it's called with non-empty $columnIdsToFetch or the GQL handles it.

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
        // The GetPortfolioItemMirrorLinkDetailsQuery is designed to fetch all column_values
        // and apply the ... on MirrorValue fragment to each, allowing us to find the specific mirror column by its ID.
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
                        Log::warning("MIRROR_FETCH_LOOP: Column '{$targetMirrorColumnId}' was found, but its type is '{$currentColumnType}', not 'mirror'. Cannot extract linked_board_id via mirrored_items.", ['column_data' => $cv]);
                        return null;
                    }

                    // The ... on MirrorValue fragment should ensure 'mirrored_items' is present if it's a mirror column with links
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
                        Log::warning("MIRROR_FETCH_FAIL: Mirror column '{$targetMirrorColumnId}' found, but 'mirrored_items' key is missing, not an array, or is empty. This might happen if the mirror column is not linked or has no items to mirror.", ['column_data' => $cv]);
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

    protected function execute(string $queryString, array $variables = []): array
    {
        $requestBody = [
            'query' => $queryString,
            'variables' => (object) $variables
        ];

        Log::debug('Monday API Request:', [
            'url' => $this->apiUrl,
            'body' => $requestBody,
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->apiToken,
            'API-Version' => '2023-10',
        ])->post($this->apiUrl, $requestBody);

        $responseData = $response->json();

        Log::debug('Monday API Response:', [
            'status' => $response->status(),
            'body' => $responseData,
            'headers' => $response->headers(),
        ]);

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
}
