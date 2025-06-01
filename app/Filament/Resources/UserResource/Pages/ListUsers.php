<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder; // Import Builder
use Filament\Resources\Components\Tab;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Define the tabs for the User resource table.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users')
                ->icon('heroicon-o-users'), // Example icon

            'agency_users' => Tab::make('Agency Users')
                ->icon('heroicon-o-user-circle') // Example icon
                ->modifyQueryUsing(function (Builder $query) {
                    // Users who have either 'admin' OR 'account_manager' role
                    return $query->whereHas('roles', function (Builder $roleQuery) {
                        $roleQuery->whereIn('name', ['admin', 'account_manager']);
                    });
                }),

            'client_users' => Tab::make('Client Users')
                ->icon('heroicon-o-user-group') // Example icon
                ->modifyQueryUsing(function (Builder $query) {
                    // Users who have the 'client_user' role
                    return $query->whereHas('roles', function (Builder $roleQuery) {
                        $roleQuery->where('name', 'client_user');
                    });
                }),
        ];
    }
}
