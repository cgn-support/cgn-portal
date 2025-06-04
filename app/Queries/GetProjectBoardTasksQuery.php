<?php

namespace App\Queries;

class GetProjectBoardTasksQuery implements QueryInterface
{
    protected string $projectBoardId;
    protected string $showInPortalColumnId;
    protected array $taskDetailColumnIds; // New property
    protected int $limit;
    protected ?string $cursor;

    public function __construct(
        string $projectBoardId,
        string $showInPortalColumnId,
        array $taskDetailColumnIds, // Added
        int $limit = 100,
        ?string $cursor = null
    ) {
        $this->projectBoardId = $projectBoardId;
        $this->showInPortalColumnId = $showInPortalColumnId;
        $this->taskDetailColumnIds = $taskDetailColumnIds; // Added
        $this->limit = $limit;
        $this->cursor = $cursor;
    }

    public function getQuery(): string
    {
        return file_get_contents(resource_path('graphql/get_project_board_tasks.graphql'));
    }

    public function getVariables(): array
    {
        $variables = [
            'projectBoardId' => [$this->projectBoardId],
            'showInPortalColumnId' => [$this->showInPortalColumnId],
            'taskDetailColumnIds' => $this->taskDetailColumnIds, // Added
            'limit' => $this->limit,
        ];
        if ($this->cursor) {
            $variables['cursor'] = $this->cursor;
        }
        return $variables;
    }

    public function setCursor(?string $cursor): void
    {
        $this->cursor = $cursor;
    }
}
