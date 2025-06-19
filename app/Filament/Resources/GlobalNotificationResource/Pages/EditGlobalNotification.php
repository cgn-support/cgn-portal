<?php

namespace App\Filament\Resources\GlobalNotificationResource\Pages;

use App\Filament\Resources\GlobalNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGlobalNotification extends EditRecord
{
    protected static string $resource = GlobalNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
