<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Support\Enums\FontWeight;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Lead Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lead Information')
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->relationship('project')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->business->name ?? 'No Business')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'valid' => 'Valid',
                                'invalid' => 'Invalid',
                                'closed' => 'Closed',
                            ])
                            ->required(),

                        Forms\Components\Toggle::make('is_valid')
                            ->label('Is Valid Lead'),

                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->visible(fn(Forms\Get $get) => $get('status') === 'closed'),

                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Form Data')
                    ->schema([
                        Forms\Components\KeyValue::make('payload')
                            ->label('Form Submission Data')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Tracking Information')
                    ->schema([
                        Forms\Components\TextInput::make('utm_source')
                            ->disabled(),
                        Forms\Components\TextInput::make('utm_medium')
                            ->disabled(),
                        Forms\Components\TextInput::make('utm_campaign')
                            ->disabled(),
                        Forms\Components\TextInput::make('referrer_name')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('submitted_at')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->getStateUsing(fn(Lead $record) => $record->name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereRaw("JSON_EXTRACT(payload, '$.first_name') LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("JSON_EXTRACT(payload, '$.last_name') LIKE ?", ["%{$search}%"]);
                    })
                    ->sortable(false)
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->getStateUsing(fn(Lead $record) => $record->email)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereRaw("JSON_EXTRACT(payload, '$.email') LIKE ?", ["%{$search}%"]);
                    })
                    ->copyable()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->getStateUsing(fn(Lead $record) => $record->phone)
                    ->copyable()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('project.business.name')
                    ->label('Business')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'new',
                        'success' => 'valid',
                        'danger' => 'invalid',
                        'primary' => 'closed',
                    ]),

                Tables\Columns\IconColumn::make('is_valid')
                    ->label('Valid')
                    ->boolean(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('utm_source')
                    ->label('Source')
                    ->badge()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('business')
                    ->label('Business')
                    ->options(function () {
                        return Business::whereHas('projects.leads')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value']) && $data['value']) {
                            $query->whereHas('project.business', function (Builder $q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'valid' => 'Valid',
                        'invalid' => 'Invalid',
                        'closed' => 'Closed',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_valid')
                    ->label('Valid Leads')
                    ->placeholder('All leads')
                    ->trueLabel('Valid only')
                    ->falseLabel('Invalid only'),

                Filter::make('submitted_date')
                    ->form([
                        Forms\Components\Select::make('period')
                            ->label('Date Range')
                            ->options([
                                'today' => 'Today',
                                'yesterday' => 'Yesterday',
                                'last_7_days' => 'Last 7 Days',
                                'last_30_days' => 'Last 30 Days',
                                'last_90_days' => 'Last 90 Days',
                                'this_month' => 'This Month',
                                'last_month' => 'Last Month',
                                'this_year' => 'This Year',
                                'custom' => 'Custom Range',
                            ])
                            ->live(),

                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->visible(fn(Forms\Get $get) => $get('period') === 'custom'),

                        Forms\Components\DatePicker::make('to')
                            ->label('To Date')
                            ->visible(fn(Forms\Get $get) => $get('period') === 'custom'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['period'])) {
                            return $query;
                        }

                        return match ($data['period']) {
                            'today' => $query->whereDate('submitted_at', Carbon::today()),
                            'yesterday' => $query->whereDate('submitted_at', Carbon::yesterday()),
                            'last_7_days' => $query->where('submitted_at', '>=', Carbon::now()->subDays(7)),
                            'last_30_days' => $query->where('submitted_at', '>=', Carbon::now()->subDays(30)),
                            'last_90_days' => $query->where('submitted_at', '>=', Carbon::now()->subDays(90)),
                            'this_month' => $query->whereMonth('submitted_at', Carbon::now()->month)
                                ->whereYear('submitted_at', Carbon::now()->year),
                            'last_month' => $query->whereMonth('submitted_at', Carbon::now()->subMonth()->month)
                                ->whereYear('submitted_at', Carbon::now()->subMonth()->year),
                            'this_year' => $query->whereYear('submitted_at', Carbon::now()->year),
                            'custom' => $query->when($data['from'], fn($q) => $q->whereDate('submitted_at', '>=', $data['from']))
                                ->when($data['to'], fn($q) => $q->whereDate('submitted_at', '<=', $data['to'])),
                            default => $query,
                        };
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['period'])) {
                            return null;
                        }

                        return match ($data['period']) {
                            'today' => 'Today',
                            'yesterday' => 'Yesterday',
                            'last_7_days' => 'Last 7 days',
                            'last_30_days' => 'Last 30 days',
                            'last_90_days' => 'Last 90 days',
                            'this_month' => 'This month',
                            'last_month' => 'Last month',
                            'this_year' => 'This year',
                            'custom' => ($data['from'] ?? '') . ' - ' . ($data['to'] ?? ''),
                            default => null,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_valid')
                    ->label('Mark Valid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Lead $record) => !$record->is_valid)
                    ->action(fn(Lead $record) => $record->markAsValid())
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('mark_invalid')
                    ->label('Mark Invalid')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Lead $record) => $record->is_valid)
                    ->action(fn(Lead $record) => $record->markAsInvalid())
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('mark_closed')
                    ->label('Mark Closed')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('primary')
                    ->visible(fn(Lead $record) => $record->status !== 'closed')
                    ->form([
                        Forms\Components\TextInput::make('value')
                            ->label('Lead Value')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->required(),
                    ])
                    ->action(function (Lead $record, array $data) {
                        $record->markAsClosed($data['value']);
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('mark_valid')
                        ->label('Mark as Valid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn(Lead $record) => $record->markAsValid());
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_invalid')
                        ->label('Mark as Invalid')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(fn(Lead $record) => $record->markAsInvalid());
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
