<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    Section::make('Filter Dashboard')
                        ->schema([
                            Select::make('project')
                                ->label('Project')
                                ->placeholder('All Projects')
                                ->options(function () {
                                    return Project::whereNotNull('project_url')
                                        ->with('business')
                                        ->get()
                                        ->mapWithKeys(function ($project) {
                                            $name = $project->business->name ?? 'Project ' . $project->id;
                                            return [$project->id => $name];
                                        })
                                        ->toArray();
                                })
                                ->searchable()
                                ->native(false),

                            Select::make('dateRange')
                                ->label('Date Range')
                                ->placeholder('Select date range')
                                ->options([
                                    'last_7_days' => 'Last 7 Days',
                                    'last_30_days' => 'Last 30 Days',
                                    'last_90_days' => 'Last 90 Days',
                                    'this_month' => 'This Month',
                                    'last_month' => 'Last Month',
                                    'this_year' => 'This Year',
                                    'custom' => 'Custom Range',
                                ])
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state !== 'custom') {
                                        // Clear custom dates when predefined range is selected
                                        $set('startDate', null);
                                        $set('endDate', null);
                                    }
                                })
                                ->native(false),

                            Section::make('Custom Date Range')
                                ->schema([
                                    DatePicker::make('startDate')
                                        ->label('Start Date')
                                        ->native(false),

                                    DatePicker::make('endDate')
                                        ->label('End Date')
                                        ->native(false),
                                ])
                                ->columns(2)
                                ->visible(fn($get) => $get('dateRange') === 'custom'),
                        ])
                        ->columns(1), // Changed from 3 to 1 for stacked layout
                ]),
        ];
    }
}
