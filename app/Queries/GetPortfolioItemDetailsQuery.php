<?php

namespace App\Queries;

class GetPortfolioItemDetailsQuery implements QueryInterface
{
    protected string $portfolioItemId;
    protected string $portfolioBoardId;
    protected array $columnIdsToFetch;

    /**
     * @param string $portfolioItemId The ID of the item (pulse) on the Portfolio board.
     * @param string $portfolioBoardId The ID of the Portfolio board.
     * @param array $columnIdsToFetch An array of column IDs to retrieve values for.
     */
    public function __construct(string $portfolioItemId, string $portfolioBoardId, array $columnIdsToFetch)
    {
        $this->portfolioItemId = $portfolioItemId;
        $this->portfolioBoardId = $portfolioBoardId;
        $this->columnIdsToFetch = $columnIdsToFetch;
    }

    public function getQuery(): string
    {
        return file_get_contents(resource_path('graphql/get_portfolio_item_details.graphql'));
    }

    public function getVariables(): array
    {
        return [
            'portfolioBoardId' => [$this->portfolioBoardId], // GraphQL expects arrays for ID types
            'portfolioItemId' => [$this->portfolioItemId],   // GraphQL expects arrays for ID types
            'columnIds' => $this->columnIdsToFetch,
        ];
    }
}
