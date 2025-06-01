<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Models\User; // For Account Manager and Client User selection
use App\Models\Business;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Projects'; // Example group

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Core Project Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('user_id') // Client User
                            ->relationship('clientUser', 'name', function (Builder $query) {
                                // Optionally filter for users with the 'client_user' role if needed
                                return $query->whereHas('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'client_user'));
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Client User'),
                        Forms\Components\Select::make('account_manager_id')
                            ->label('Account Manager')
                            ->relationship(
                                name: 'accountManager',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->whereHas('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'account_manager'))
                            )
                            ->searchable()
                            ->preload()
                            ->required(), // Or nullable
                        Forms\Components\Select::make('business_id')
                            ->relationship('business', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('plan_id')
                            ->relationship('plan', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\DatePicker::make('project_start_date')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('active')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Monday.com Integration Details')
                    ->columns(2)
                    ->description('Enter the Pulse ID from the "Active Clients" Portfolio board item. Other fields will be populated by "Fetch Monday Data".')
                    ->schema([
                        Forms\Components\TextInput::make('monday_pulse_id')
                            ->label('Monday Pulse ID (Portfolio Item)')
                            ->helperText('The ID of the item on the "Active Clients" Portfolio board.')
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('monday_board_id')
                            ->label('Monday Project Board ID')
                            ->disabled()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('portfolio_project_rag')
                            ->label('Portfolio RAG Status')
                            ->disabled()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('portfolio_project_doc')
                            ->label('Portfolio Project Doc (Title/Link)')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('portfolio_project_scope')
                            ->label('Portfolio Project Scope')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Synced Project Data')
                    ->columns(2)
                    ->description('These fields are typically synced from Monday.com.')
                    ->schema([
                        Forms\Components\TextInput::make('project_url')
                            ->label('Project URL (Domain)')
                            ->url()
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TagsInput::make('current_services')
                            ->label('Current Services')
                            ->nullable(),
                        Forms\Components\TagsInput::make('completed_services')
                            ->label('Completed Services')
                            ->nullable(),
                        Forms\Components\TextInput::make('client_logo')
                            ->label('Client Logo URL')
                            ->url()
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('google_sheet_id')
                            ->label('Google Sheet ID (Workbook)')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('slack_channel')
                            ->label('Slack Channel ID/Name')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('bright_local_url')
                            ->label('Bright Local ID/URL')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('google_drive_folder')
                            ->label('Google Drive Folder ID')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('wp_umbrella_project_id')
                            ->label('WP Umbrella ID')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('my_maps_share_link')
                            ->label('My Maps Share Link')
                            ->url()
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('specialist_monday_id')->label('Specialist (Monday User ID)')->disabled()->nullable(),
                        Forms\Components\TextInput::make('content_writer_monday_id')->label('Content Writer (Monday User ID)')->disabled()->nullable(),
                        Forms\Components\TextInput::make('developer_monday_id')->label('Developer (Monday User ID)')->disabled()->nullable(),
                        Forms\Components\TextInput::make('copywriter_monday_id')->label('Copywriter (Monday User ID)')->disabled()->nullable(),
                        Forms\Components\TextInput::make('designer_monday_id')->label('Designer (Monday User ID)')->disabled()->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clientUser.name')
                    ->label('Client User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accountManager.name')
                    ->label('Account Manager')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\LeadsRelationManager::class,
            // RelationManagers\NotesRelationManager::class,
            // RelationManagers\ReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
