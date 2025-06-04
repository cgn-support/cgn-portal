<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
// use App\Filament\Resources\UserResource\RelationManagers; // Uncomment if you have relation managers
use App\Models\User;
use App\Models\Client; // For Client selection options, if needed
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get; // For reactive visibility
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // For role selection and checking

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->maxLength(255)
                            ->helperText('Leave blank to keep current password when editing. Required on create.'),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->nullable(),
                    ]),
                Forms\Components\Section::make('Roles & Assignments')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->preload()
                            ->live() // Important for conditional visibility of other fields
                            ->helperText('Assign one or more roles to this user.'),

                        Forms\Components\Select::make('client_id')
                            ->label('Assign to Client Company')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(function (Get $get): bool {
                                $selectedRoleIds = $get('roles');
                                if (!is_array($selectedRoleIds) || empty($selectedRoleIds)) {
                                    return false;
                                }
                                $selectedRoleNames = Role::whereIn('id', $selectedRoleIds)->pluck('name')->all();
                                return in_array('client_user', $selectedRoleNames);
                            })
                            ->helperText('Only applicable if the user has the "client_user" role.'),

                        Forms\Components\TextInput::make('monday_user_id')
                            ->label('Monday.com User ID')
                            ->nullable()
                            ->numeric()
                            ->unique(ignoreRecord: true, table: 'users', column: 'monday_user_id')
                            ->visible(function (Get $get): bool {
                                $selectedRoleIds = $get('roles');
                                if (!is_array($selectedRoleIds) || empty($selectedRoleIds)) {
                                    return false;
                                }
                                $selectedRoleNames = Role::whereIn('id', $selectedRoleIds)->pluck('name')->all();
                                // Show for admin or account_manager
                                return in_array('admin', $selectedRoleNames) || in_array('account_manager', $selectedRoleNames);
                            })
                            ->helperText('Enter the numeric User ID from Monday.com. Required for Account Managers to sync project assignments.'),
                    ])
            ]);
    }

    // ... rest of your UserResource (table, getRelations, getPages, etc.) remains the same as previously updated ...
    // Make sure the table method also includes a column for 'monday_user_id' if you want to see it there.
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')->badge()->label('Roles')->searchable(isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('client.name')->label('Client Company')->searchable()->sortable()->placeholder('N/A'),
                Tables\Columns\TextColumn::make('monday_user_id')->label('Monday ID')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true), // Added to table
                Tables\Columns\IconColumn::make('email_verified_at')->label('Verified')->boolean()->sortable()->getStateUsing(fn(User $record): bool => (bool) $record->email_verified_at),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('M j, Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('client')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
