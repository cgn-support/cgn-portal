<?php

namespace App\Filament\Resources\PresetTaskResource\Pages;

use App\Filament\Resources\PresetTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresetTasks extends ListRecords
{
    protected static string $resource = PresetTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
