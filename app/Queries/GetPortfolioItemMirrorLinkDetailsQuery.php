<?php

namespace App\Queries;

class GetPortfolioItemMirrorLinkDetailsQuery implements QueryInterface
{
    protected string $portfolioItemId;
    protected string $portfolioBoardId;
    public function __construct(string $portfolioItemId, string $portfolioBoardId)
    { // Removed $mirrorColumnId
        $this->portfolioItemId = $portfolioItemId;
        $this->portfolioBoardId = $portfolioBoardId;
    }
    public function getQuery(): string
    {
        return file_get_contents(resource_path('graphql/get_portfolio_item_mirror_link_details.graphql'));
    }
    public function getVariables(): array
    {
        return [
            'portfolioItemId' => [$this->portfolioItemId],
            'portfolioBoardId' => [$this->portfolioBoardId]
        ];
    }
}
