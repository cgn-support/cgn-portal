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
use App\Events\ReportPublished;
use Filament\Notifications\Notification;

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
                        Forms\Components\TextInput::make('looker_studio_share_link')
                            ->label('Looker Studio Share Link')
                            ->helperText('Share link to the full Looker Studio report dashboard')
                            ->url()
                            ->placeholder('https://lookerstudio.google.com/...')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Manual Metrics')
                    ->description('Enter metrics that cannot be automatically tracked. Automated metrics (organic sessions, form submissions, web phone calls, contact button users) will be pulled from your tracking system.')
                    ->schema([
                        Forms\Components\TextInput::make('metrics_data.gbp_phone_calls')
                            ->label('GBP Phone Calls')
                            ->helperText('Phone calls from Google Business Profile')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('metrics_data.gbp_listing_clicks')
                            ->label('GBP Listing Clicks')
                            ->helperText('Website clicks from Google Business Profile')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('metrics_data.gbp_booking_clicks')
                            ->label('GBP Booking Clicks')
                            ->helperText('Booking button clicks from Google Business Profile')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('metrics_data.total_citations')
                            ->label('Total Citations')
                            ->helperText('Current total number of business citations')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('metrics_data.total_reviews')
                            ->label('Total Reviews')
                            ->helperText('Current total number of online reviews')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->collapsible(),
                    
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
                Tables\Actions\Action::make('publish')
                    ->label('Publish Report')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Publish Report')
                    ->modalDescription('This will send email and Slack notifications to the client. Are you sure you want to publish this report?')
                    ->modalSubmitActionLabel('Yes, Publish Report')
                    ->action(function (Report $record) {
                        // Update status to sent
                        $record->update(['status' => 'sent']);
                        
                        // Fire the event to send notifications
                        ReportPublished::dispatch($record);
                        
                        Notification::make()
                            ->title('Report Published Successfully')
                            ->body('Notifications have been sent to the client via email and Slack.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Report $record): bool => $record->status !== 'sent'),
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
