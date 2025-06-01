<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope; // Spatie's Permission model doesn't use SoftDeletes by default
use Illuminate\Support\Facades\Config;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key'; // Example icon
    protected static ?string $navigationGroup = 'Admin Management'; // Same group as Roles

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Permission Name')
                            ->minLength(2)
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true) // Ensure permission name is unique for the guard
                            ->helperText('A unique name for the permission, e.g., "create projects" or "edit users". Use lowercase and underscores or spaces.'),

                        Forms\Components\Select::make('guard_name')
                            ->label('Guard Name')
                            ->options(self::getGuardNames())
                            ->default(Config::get('auth.defaults.guard'))
                            ->required()
                            ->helperText('Usually "web" for web interface permissions, or "api" for API permissions.'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Permission Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles_count') // Display count of roles that have this permission
                    ->counts('roles')
                    ->label('Roles Count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Permission $record) {
                        // Prevent deleting permissions that are in use by roles
                        if ($record->roles()->count() > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Permission In Use')
                                ->body("This permission is assigned to {$record->roles()->count()} roles and cannot be deleted.")
                                ->danger()
                                ->send();
                            return false; // Halts the action
                        }
                        // You might also want to check if it's directly assigned to any users,
                        // though direct user-permission assignment is less common than through roles.
                        // if ($record->users()->count() > 0) { ... }
                        return true;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                            foreach ($records as $record) {
                                if ($record->roles()->count() > 0) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Permission In Use')
                                        ->body("Permission '{$record->name}' is assigned to roles and cannot be deleted in bulk.")
                                        ->danger()
                                        ->send();
                                    return false; // Halts the action
                                }
                            }
                            return true;
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // You could add a RelationManager to show which roles have this permission
            // RelationManagers\RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    /**
     * Get the available guard names from the auth config.
     *
     * @return array
     */
    protected static function getGuardNames(): array
    {
        $guards = Config::get('auth.guards');
        $guardNames = [];
        foreach ($guards as $guardName => $config) {
            $guardNames[$guardName] = $guardName;
        }
        return $guardNames;
    }
}
