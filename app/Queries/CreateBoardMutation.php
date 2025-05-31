<?php

namespace App\Queries;

class CreateBoardMutation implements QueryInterface
{
    protected string $boardName;

    protected string $boardKind; // e.g., "public", "private", "share" [cite: 1]

    protected ?int $templateId;   // Template ID is an integer in Monday.com

    protected array $ownerIds;   // Array of Monday.com user IDs

    protected ?int $workspaceId; // Optional: Workspace ID

    /**
     * @param string $boardName Name for the new board.
     * @param string $boardKind Kind of board ("public", "private", "share"). [cite: 1]
     * @param int|null $templateId ID of the template to use. [cite: 1]
     * @param array $ownerIds Array of user IDs to be board owners. [cite: 1]
     * @param int|null $workspaceId Optional ID of the workspace to create the board in.
     */
    public function __construct(
        string $boardName,
        string $boardKind,
        ?int   $templateId = null,
        array  $ownerIds = [],
        ?int   $workspaceId = null
    )
    {
        $this->boardName = $boardName;
        $this->boardKind = $boardKind;
        $this->templateId = $templateId;
        $this->ownerIds = $ownerIds;
        $this->workspaceId = $workspaceId;
    }

    public function getQuery(): string
    {
        return file_get_contents(resource_path('graphql/create_board.graphql'));
    }

    public function getVariables(): array
    {
        $variables = [
            'boardName' => $this->boardName,
            'boardKind' => $this->boardKind,
        ];

        if ($this->templateId !== null) {
            $variables['templateId'] = $this->templateId;
        }
        if (!empty($this->ownerIds)) {
            // Ensure owner IDs are strings if your GraphQL schema expects [ID!] and IDs are numeric
            $variables['ownerIds'] = array_map('strval', $this->ownerIds);
        }
        if ($this->workspaceId !== null) {
            $variables['workspaceId'] = $this->workspaceId;
        }

        return $variables;
    }
}
