<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Leads'),
            'new' => Tab::make('New')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'new'))
                ->badge(fn() => $this->getModel()::where('status', 'new')->count()),
            'valid' => Tab::make('Valid')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'valid'))
                ->badge(fn() => $this->getModel()::where('status', 'valid')->count()),
            'closed' => Tab::make('Closed')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'closed'))
                ->badge(fn() => $this->getModel()::where('status', 'closed')->count()),
        ];
    }
}
