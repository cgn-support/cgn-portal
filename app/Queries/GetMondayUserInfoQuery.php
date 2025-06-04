<?php

namespace App\Queries;

class GetMondayUserInfoQuery implements QueryInterface
{
    protected array $mondayUserIds;

    /**
     * @param array $mondayUserIds An array containing the Monday.com user ID(s).
     * The GraphQL query for users takes an array of IDs.
     */
    public function __construct(array $mondayUserIds)
    {
        $this->mondayUserIds = $mondayUserIds;
    }

    public function getQuery(): string
    {
        return file_get_contents(resource_path('graphql/get_monday_user_info.graphql'));
    }

    public function getVariables(): array
    {
        // Ensure IDs are strings if they are numeric, as GraphQL ID type can be string or int
        // but usually passed as string in variables. Spatie's default User ID is typically int.
        // Monday.com User IDs are integers. The GQL schema [ID!] will handle numeric IDs fine.
        return ['userIds' => array_map('intval', $this->mondayUserIds)];
    }
}
