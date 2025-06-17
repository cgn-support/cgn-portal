<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static ?string $navigationGroup = 'Client Management';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Details')
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'project_url')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Select::make('account_manager_id')
                            ->relationship('accountManager', 'name')
                            ->searchable()
                            ->preload()
                            ->default(auth()->id())
                            ->columnSpan(1),
                        Forms\Components\Select::make('report_month')
                            ->label('Report Month')
                            ->options([
                                1 => 'January',
                                2 => 'February', 
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December',
                            ])
                            ->default(now()->subMonth()->month)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $monthName = date('F', mktime(0, 0, 0, $state, 1));
                                    $year = now()->year;
                                    $set('title', "Marketing Report For {$monthName} {$year}");
                                }
                            })
                            ->columnSpan(1),
                        Forms\Components\Hidden::make('title')
                            ->default(function () {
                                $monthName = date('F', mktime(0, 0, 0, now()->subMonth()->month, 1));
                                $year = now()->year;
                                return "Marketing Report For {$monthName} {$year}";
                            }),
                        Forms\Components\DatePicker::make('report_date')
                            ->label('Date Created')
                            ->required()
                            ->default(now())
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'reviewed' => 'Reviewed',
                            ])
                            ->default('draft')
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Report Content')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Description & Highlights')
                            ->placeholder('A lot of success this month. The highlights are:')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Analytics Screenshot')
                            ->image()
                            ->imageEditor()
                            ->directory('reports/screenshots')
                            ->visibility('private')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(5120) // 5MB
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Internal Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Private Notes')
                            ->placeholder('Internal notes not visible to client...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('report_month_name')
                    ->label('Report Month')
                    ->sortable('report_month')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('project.project_url')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accountManager.name')
                    ->label('Account Manager')
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_date')
                    ->label('Report Date')
                    ->date('M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'sent',
                        'primary' => 'reviewed',
                    ]),
                Tables\Columns\IconColumn::make('file_path')
                    ->label('Screenshot')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->file_path)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'reviewed' => 'Reviewed',
                    ]),
                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'project_url'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Report')
                    ->url(fn (Report $record): string => route('project.report', [
                        'uuid' => $record->project_id,
                        'report_id' => $record->id
                    ]))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('report_date', 'desc');
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
