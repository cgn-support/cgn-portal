<?php

namespace App\Filament\Resources\PresetTaskResource\Pages;

use App\Filament\Resources\PresetTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPresetTask extends EditRecord
{
    protected static string $resource = PresetTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
