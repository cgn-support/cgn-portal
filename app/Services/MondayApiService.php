<?php

namespace App\Services;

use App\Queries\CreateBoardMutation;
use App\Queries\QueryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// <-- Add this import

class MondayApiService
{
    protected string $apiToken;
    protected string $apiUrl;

    /**
     * Constructor for MondayApiService.
     *
     * @param string $apiToken The API token for Monday.com.
     * @param string $apiUrl The API URL for Monday.com.
     */
    public function __construct(string $apiToken, string $apiUrl)
    {
        $this->apiToken = $apiToken;
        $this->apiUrl = $apiUrl;
    }

    /**
     * Executes a given GraphQL query object.
     *
     * @param QueryInterface $query The query object implementing QueryInterface.
     * @return array The JSON decoded response data from the API.
     * @throws \RuntimeException If the API request fails or returns GraphQL errors.
     */
    public function executeQuery(QueryInterface $query): array
    {
        return $this->execute($query->getQuery(), $query->getVariables());
    }

    /**
     * Creates a new board on Monday.com from a template.
     *
     * @param string $boardName Name for the new board.
     * @param string $boardKind Kind of board ("public", "private", "share"). [cite: 1]
     * @param int|null $templateId ID of the template to use. [cite: 1]
     * @param array $ownerIds Array of Monday.com user IDs to be board owners. [cite: 1]
     * @param int|null $workspaceId Optional ID of the workspace to create the board in.
     * @return array The 'data' portion of the API response, typically containing the new board's id and name.
     * @throws \RuntimeException If the API request fails or returns GraphQL errors.
     */
    public function createBoardFromTemplate(
        string $boardName,
        string $boardKind, // e.g., "private"
        ?int   $templateId = null,
        array  $ownerIds = [],
        ?int   $workspaceId = null
    ): array
    {
        $mutation = new CreateBoardMutation(
            $boardName,
            $boardKind,
            $templateId,
            $ownerIds,
            $workspaceId
        );

        $response = $this->executeQuery($mutation);

        // The actual board data is usually within response['data']['create_board']
        if (isset($response['data']['create_board'])) {
            return $response['data']['create_board'];
        }

        Log::warning('create_board mutation did not return the expected data structure.', [
            'response' => $response
        ]);
        // Or throw an exception if this structure is critical
        throw new \RuntimeException('Failed to create board or parse response correctly. "create_board" key missing in data.');
    }


    /**
     * Protected method to make the actual HTTP request to the Monday.com API.
     *
     * @param string $queryString The GraphQL query string.
     * @param array<string, mixed> $variables The variables for the GraphQL query.
     * @return array The JSON decoded response data.
     * @throws \RuntimeException If the API request fails or returns GraphQL errors.
     */
    protected function execute(string $queryString, array $variables = []): array
    {
        $requestBody = [
            'query' => $queryString,
            'variables' => (object)$variables
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
            Log::error('Monday API HTTP Error:', [
                'status' => $response->status(),
                'response_body' => $responseData,
                'request_query' => $queryString,
                'request_variables' => $variables
            ]);
            throw new \RuntimeException("Monday API HTTP request failed with status {$response->status()}: " . ($response->body() ?: 'No response body'));
        }

        if (isset($responseData['errors']) && !empty($responseData['errors'])) {
            Log::error('Monday API GraphQL Error:', [
                'errors' => $responseData['errors'],
                'request_query' => $queryString,
                'request_variables' => $variables
            ]);
            throw new \RuntimeException("Monday API GraphQL error: " . json_encode($responseData['errors']));
        }

        if (!isset($responseData['data']) && empty($responseData['errors'])) {
            Log::warning('Monday API Response missing "data" key and no GraphQL errors reported:', [
                'response_body' => $responseData,
                'request_query' => $queryString,
                'request_variables' => $variables
            ]);
        }

        return $responseData;
    }

    /**
     * Utility function to safely extract a value from a Monday.com column_values array.
     * This is a placeholder and needs to be adapted based on the actual structure of your column data.
     *
     * @param array $columnValues The array of column_values from a Monday.com item.
     * @param string $targetColumnId The ID of the column you want to retrieve.
     * @param string $valueKey The key within the column object that holds the desired value (e.g., 'text', 'value').
     * @return mixed|null The value of the column or null if not found.
     */
    protected function getColumnValueById(array $columnValues, string $targetColumnId, string $valueKey = 'text')
    {
        foreach ($columnValues as $column) {
            if (isset($column['id']) && $column['id'] === $targetColumnId) {
                if ($valueKey === 'value' && isset($column['value'])) {
                    $decodedValue = json_decode($column['value'], true);
                    return (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) ? $decodedValue : $column['value'];
                }
                return $column[$valueKey] ?? null;
            }
        }
        return null;
    }
}
