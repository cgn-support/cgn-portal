<?php

namespace App\Services;

use App\Queries\QueryInterface;
use App\Queries\CreateBoardMutation;
use App\Queries\GetPortfolioItemDetailsQuery;
use App\Queries\GetPortfolioItemMirrorLinkDetailsQuery;
use App\Queries\GetMondayUserInfoQuery;
use App\Queries\GetProjectBoardTasksQuery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MondayApiService
{
    protected string $apiToken;
    protected string $apiUrl;
    protected string $portfolioBoardId;
    
    protected const API_VERSION = '2023-10';
    protected const DEFAULT_PAGE_LIMIT = 100;
    protected const MAX_PAGES = 10;
    protected const DEFAULT_SHOW_IN_PORTAL_VALUE = 'yes';

    public function __construct(string $apiToken, string $apiUrl, string $portfolioBoardId)
    {
        $this->apiToken = $apiToken;
        $this->apiUrl = $apiUrl;
        $this->portfolioBoardId = $portfolioBoardId;
    }

    public function executeQuery(QueryInterface $query): array
    {
        return $this->execute($query->getQuery(), $query->getVariables());
    }

    public function createBoardFromTemplate(
        string $boardName, 
        string $boardKind, 
        ?int $templateId = null, 
        array $ownerIds = [], 
        ?int $workspaceId = null
    ): array {
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
        if (empty($portfolioItemId)) {
            throw new \InvalidArgumentException('Portfolio item ID cannot be empty');
        }
        
        $columnIdsToFetch = array_filter($columnIdsToFetch, fn($id) => $id !== 'name' && !empty($id));
        $query = new GetPortfolioItemDetailsQuery($portfolioItemId, $this->portfolioBoardId, $columnIdsToFetch);
        
        try {
            $response = $this->executeQuery($query);
            $items = $response['data']['boards'][0]['items_page']['items'] ?? [];
            
            if (empty($items)) {
                Log::warning("No item found on portfolio board for item ID: {$portfolioItemId}");
                return null;
            }
            
            return $items[0];
        } catch (\Exception $e) {
            Log::error("Error fetching portfolio item details for ID {$portfolioItemId}: " . $e->getMessage());
            throw $e;
        }
    }

    public function getLinkedBoardIdFromMirrorColumn(string $portfolioItemId, string $targetMirrorColumnId): ?string
    {
        $query = new GetPortfolioItemMirrorLinkDetailsQuery($portfolioItemId, $this->portfolioBoardId);
        try {
            $response = $this->executeQuery($query);
            $items = $response['data']['boards'][0]['items_page']['items'] ?? [];
            
            if (empty($items)) {
                Log::warning("No item found for portfolio item ID: {$portfolioItemId}");
                return null;
            }
            
            return $this->extractLinkedBoardIdFromMirrorColumn($items[0], $targetMirrorColumnId);
        } catch (\Exception $e) {
            Log::error("Error fetching linked board ID from mirror column for portfolio item ID {$portfolioItemId}: " . $e->getMessage());
            throw $e;
        }
    }
    
    protected function extractLinkedBoardIdFromMirrorColumn(array $item, string $targetMirrorColumnId): ?string
    {
        $columnValues = $item['column_values'] ?? [];
        
        if (empty($columnValues)) {
            return null;
        }
        
        foreach ($columnValues as $columnValue) {
            if ($this->isMirrorColumnMatch($columnValue, $targetMirrorColumnId)) {
                return $this->extractLinkedBoardId($columnValue);
            }
        }
        
        return null;
    }
    
    protected function isMirrorColumnMatch(array $columnValue, string $targetColumnId): bool
    {
        return ($columnValue['id'] ?? null) === $targetColumnId && ($columnValue['type'] ?? null) === 'mirror';
    }
    
    protected function extractLinkedBoardId(array $mirrorColumn): ?string
    {
        $mirroredItems = $mirrorColumn['mirrored_items'] ?? [];
        
        if (empty($mirroredItems) || !isset($mirroredItems[0]['linked_board_id'])) {
            return null;
        }
        
        return (string) $mirroredItems[0]['linked_board_id'];
    }

    public function getMondayUserProfilePhoto(string $mondayUserId): ?string
    {
        if (empty(trim($mondayUserId)) || !is_numeric($mondayUserId)) {
            throw new \InvalidArgumentException('Monday User ID must be a non-empty numeric string');
        }
        
        $query = new GetMondayUserInfoQuery([(int)$mondayUserId]);
        
        try {
            $response = $this->executeQuery($query);
            $users = $response['data']['users'] ?? [];
            
            if (!empty($users) && isset($users[0]['photo_small'])) {
                return $users[0]['photo_small'] ?: null;
            }
            
            Log::warning("No photo found for Monday User ID: {$mondayUserId}");
            return null;
        } catch (\Exception $e) {
            Log::error("Error fetching Monday user photo for ID {$mondayUserId}: " . $e->getMessage());
            return null;
        }
    }

    public function getTasksToShowInPortal(
        string $projectBoardId,
        string $showInPortalColumnId,
        array $taskDetailColumnIds = [],
        string $showInPortalTextValue = self::DEFAULT_SHOW_IN_PORTAL_VALUE,
        int $limitPerPage = self::DEFAULT_PAGE_LIMIT
    ): array {
        $allMatchingTasks = [];
        $cursor = null;
        $pageNumber = 0;

        try {
            do {
                $pageNumber++;
                $query = new GetProjectBoardTasksQuery(
                    $projectBoardId,
                    $showInPortalColumnId,
                    $taskDetailColumnIds,
                    $limitPerPage,
                    $cursor
                );

                $response = $this->executeQuery($query);
                $boardData = $response['data']['boards'][0] ?? null;

                if (!$boardData || !isset($boardData['items_page']['items'])) {
                    Log::warning("No items found for Project Board ID: {$projectBoardId} on page {$pageNumber}");
                    break;
                }

                $itemsOnPage = $boardData['items_page']['items'];
                
                if (empty($itemsOnPage)) {
                    break;
                }

                $matchingTasks = $this->filterTasksToShowInPortal(
                    $itemsOnPage, 
                    $showInPortalColumnId, 
                    $showInPortalTextValue
                );
                
                $allMatchingTasks = array_merge($allMatchingTasks, $matchingTasks);
                $cursor = $boardData['items_page']['cursor'] ?? null;
                
            } while ($cursor && $pageNumber < self::MAX_PAGES);

            if ($pageNumber >= self::MAX_PAGES && $cursor) {
                Log::warning("Reached max page limit for board {$projectBoardId}. There might be more tasks.");
            }

            Log::info("Found " . count($allMatchingTasks) . " tasks to show in portal for board {$projectBoardId}");
            return $allMatchingTasks;
        } catch (\Exception $e) {
            Log::error("Error fetching tasks for Project Board ID {$projectBoardId}: " . $e->getMessage());
            throw $e;
        }
    }
    
    protected function filterTasksToShowInPortal(
        array $tasks, 
        string $showInPortalColumnId, 
        string $showInPortalTextValue
    ): array {
        $matchingTasks = [];
        
        foreach ($tasks as $task) {
            $showInPortalColumn = $this->findColumnValueById($task['column_values'] ?? [], $showInPortalColumnId);
            
            if ($this->shouldShowTaskInPortal($showInPortalColumn, $showInPortalTextValue)) {
                $matchingTasks[] = [
                    'id' => $task['id'],
                    'name' => $task['name'],
                    'column_values' => $task['details'] ?? [],
                ];
            }
        }
        
        return $matchingTasks;
    }
    
    protected function findColumnValueById(array $columnValues, string $targetColumnId): ?array
    {
        foreach ($columnValues as $columnValue) {
            if (($columnValue['id'] ?? null) === $targetColumnId) {
                return $columnValue;
            }
        }
        
        return null;
    }
    
    protected function shouldShowTaskInPortal(?array $columnValue, string $expectedValue): bool
    {
        if (!$columnValue || !isset($columnValue['text'])) {
            return false;
        }
        
        return strtolower(trim($columnValue['text'])) === strtolower(trim($expectedValue));
    }

    protected function execute(string $queryString, array $variables = []): array
    {
        $requestBody = [
            'query' => $queryString,
            'variables' => (object) $variables
        ];
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->apiToken,
            'API-Version' => self::API_VERSION,
        ])->post($this->apiUrl, $requestBody);
        
        $responseData = $response->json();
        
        $this->validateResponse($response, $responseData, $queryString, $variables);
        
        return $responseData;
    }
    
    protected function validateResponse($response, array $responseData, string $queryString, array $variables): void
    {
        if (!$response->successful()) {
            Log::error('Monday API HTTP Error', [
                'status' => $response->status(),
                'response_body' => $responseData,
                'query' => $queryString
            ]);
            throw new \RuntimeException(
                "Monday API HTTP request failed with status {$response->status()}: " . 
                ($response->body() ?: 'No response body')
            );
        }
        
        if (isset($responseData['errors']) && !empty($responseData['errors'])) {
            Log::error('Monday API GraphQL Error', [
                'errors' => $responseData['errors'],
                'query' => $queryString
            ]);
            throw new \RuntimeException(
                "Monday API GraphQL error: " . json_encode($responseData['errors'])
            );
        }
        
        if (!isset($responseData['data']) && empty($responseData['errors'])) {
            Log::warning('Monday API Response missing "data" key', [
                'response_body' => $responseData,
                'query' => $queryString
            ]);
        }
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