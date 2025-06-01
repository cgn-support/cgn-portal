<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('primary_contact_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('primary_contact_email')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('primary_contact_phone')
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('primary_contact_title')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('preferred_comms_method')
                    ->options([
                        'slack' => 'Slack',
                        'email' => 'Email',
                        'phone' => 'Phone',
                        'text' => 'Text',
                    ])
                    ->default('slack'),
                Forms\Components\DatePicker::make('signing_date'),
                Forms\Components\TextInput::make('hubspot_company_record')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Radio::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'archived' => 'Archived',
                        'on_hold' => 'On Hold',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('primary_contact_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('primary_contact_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('primary_contact_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('primary_contact_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('preferred_comms_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hubspot_company_record')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
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
            RelationManagers\BusinessesRelationManager::class,
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
