<?php


namespace App\Queries;

interface QueryInterface
{
    /**
     * Returns a GraphQL query string for the Monday.com API.
     *
     * @return string The GraphQL query string.
     */
    public function getQuery(): string;

    /**
     * Returns an array of variables to be passed with the GraphQL query.
     *
     * If the query does not require variables, this method should return an empty array.
     *
     * @return array<string, mixed> The variables for the GraphQL query.
     */
    public function getVariables(): array;
}
