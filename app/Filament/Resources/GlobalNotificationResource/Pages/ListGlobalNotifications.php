<?php

namespace App\Filament\Resources\GlobalNotificationResource\Pages;

use App\Filament\Resources\GlobalNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGlobalNotifications extends ListRecords
{
    protected static string $resource = GlobalNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
