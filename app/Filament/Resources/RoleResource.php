<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // For populating select options
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope; // If your Role model uses SoftDeletes (Spatie's doesn't by default)
use Illuminate\Support\Facades\Config; // To get the guard name

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group'; // Example icon
    protected static ?string $navigationGroup = 'Admin Management'; // Example group

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Role Name')
                            ->minLength(2)
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true) // Ensure role name is unique
                            ->helperText('A unique name for the role, e.g., "account_manager" or "Client User".'),

                        Forms\Components\Select::make('guard_name')
                            ->label('Guard Name')
                            ->options(self::getGuardNames())
                            ->default(Config::get('auth.defaults.guard'))
                            ->required()
                            ->helperText('Usually "web" for web interface roles, or "api" for API roles.'),

                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Permissions')
                            ->relationship('permissions', 'name') // Assumes 'permissions' relationship on Role model
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3) // Adjust number of columns as needed
                            ->helperText('Select the permissions this role should have.'),
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
                    ->label('Role Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions') // Display count of permissions
                    ->label('Permissions Count')
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
                // Add filters if needed, e.g., by guard_name
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Role $record) {
                        // Prevent deleting roles that are in use, or handle gracefully
                        if ($record->users()->count() > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Role In Use')
                                ->body("This role is assigned to {$record->users()->count()} users and cannot be deleted.")
                                ->danger()
                                ->send();
                            return false; // Halts the action
                        }
                        return true;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                            foreach ($records as $record) {
                                if ($record->users()->count() > 0) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Role In Use')
                                        ->body("Role '{$record->name}' is assigned to users and cannot be deleted in bulk.")
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
            // If you want a table of permissions within the Role Edit page:
            // RelationManagers\PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
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
