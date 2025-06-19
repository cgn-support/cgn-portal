<?php

namespace App\Filament\Resources\ClientTaskResource\Pages;

use App\Filament\Resources\ClientTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientTasks extends ListRecords
{
    protected static string $resource = ClientTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
