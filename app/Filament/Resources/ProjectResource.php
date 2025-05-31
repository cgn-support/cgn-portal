<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Account Manager')
                    ->required(),
                Forms\Components\Select::make('business_id')
                    ->relationship('business', 'name')
                    ->required(),
                Forms\Components\Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('monday_pulse_id')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('monday_board_id')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('portfolio_project_rag')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('portfolio_project_doc')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('portfolio_project_scope')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('client_logo')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('google_sheet_id')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('slack_channel')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('bright_local_url')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('project_start_date'),
                Forms\Components\TextInput::make('project_url')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('google_drive_folder')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('my_maps_share_link')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('wp_umbrella_project_id')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monday_pulse_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monday_board_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('portfolio_project_rag')
                    ->searchable(),
                Tables\Columns\TextColumn::make('portfolio_project_scope')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('google_sheet_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slack_channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bright_local_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('google_drive_folder')
                    ->searchable(),
                Tables\Columns\TextColumn::make('my_maps_share_link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wp_umbrella_project_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
