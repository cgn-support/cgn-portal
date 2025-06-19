<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GlobalNotificationResource\Pages;
use App\Filament\Resources\GlobalNotificationResource\RelationManagers;
use App\Models\GlobalNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GlobalNotificationResource extends Resource
{
    protected static ?string $model = GlobalNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    
    protected static ?string $navigationLabel = 'Global Notifications';
    
    protected static ?string $modelLabel = 'Global Notification';
    
    protected static ?string $pluralModelLabel = 'Global Notifications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                
                Forms\Components\Select::make('type')
                    ->options([
                        'announcement' => 'Company Announcement',
                        'feature' => 'New Feature',
                        'blog' => 'Blog Post',
                        'podcast' => 'Podcast',
                        'video' => 'Video/YouTube',
                        'general' => 'General',
                    ])
                    ->default('general')
                    ->required(),
                
                Forms\Components\TextInput::make('icon')
                    ->label('Custom Icon (Heroicon name)')
                    ->placeholder('heroicon-o-bell')
                    ->helperText('Leave empty to use default icon based on type'),
                
                Forms\Components\TextInput::make('link')
                    ->label('Link URL')
                    ->url()
                    ->placeholder('https://example.com')
                    ->helperText('Optional link for users to click'),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Only active notifications will be shown to users'),
                
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publish Date')
                    ->helperText('Leave empty to publish immediately')
                    ->native(false),
                
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Expiration Date')
                    ->helperText('Leave empty for no expiration')
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'announcement',
                        'success' => 'feature',
                        'info' => 'blog',
                        'warning' => 'podcast',
                        'danger' => 'video',
                        'secondary' => 'general',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'announcement' => 'Company Announcement',
                        'feature' => 'New Feature',
                        'blog' => 'Blog Post',
                        'podcast' => 'Podcast',
                        'video' => 'Video/YouTube',
                        'general' => 'General',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Immediate'),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'announcement' => 'Announcement',
                        'feature' => 'Feature',
                        'blog' => 'Blog',
                        'podcast' => 'Podcast',
                        'video' => 'Video',
                        'general' => 'General',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('created_at', 'desc')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGlobalNotifications::route('/'),
            'create' => Pages\CreateGlobalNotification::route('/create'),
            'edit' => Pages\EditGlobalNotification::route('/{record}/edit'),
        ];
    }
}
