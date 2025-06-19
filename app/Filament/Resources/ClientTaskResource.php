<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientTaskResource\Pages;
use App\Filament\Resources\ClientTaskResource\RelationManagers;
use App\Models\ClientTask;
use App\Models\PresetTask;
use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientTaskResource extends Resource
{
    protected static ?string $model = ClientTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Task Management';
    protected static ?string $navigationLabel = 'Client Tasks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->business->name ?? 'Unknown Business')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('preset_task_id')
                    ->relationship('presetTask', 'title', 
                        fn (Builder $query) => $query->active()->ordered()
                    )
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $presetTask = PresetTask::find($state);
                            if ($presetTask) {
                                $set('title', $presetTask->title);
                                $set('description', $presetTask->description);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(4),
                Forms\Components\TextInput::make('link')
                    ->url()
                    ->maxLength(255)
                    ->helperText('Optional link to Google Doc, website, etc.'),
                Forms\Components\DateTimePicker::make('due_date')
                    ->helperText('Optional due date'),
                Forms\Components\Hidden::make('assigned_by')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['project.business', 'presetTask', 'assignedBy']))
            ->columns([
                Tables\Columns\TextColumn::make('project.business.name')
                    ->label('Business')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('presetTask.title')
                    ->label('Template')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedBy.name')
                    ->label('Assigned By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_completed')
                    ->options([
                        1 => 'Completed',
                        0 => 'Pending',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('project_id')
                    ->relationship('project', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->business->name ?? 'Unknown Business')
                    ->label('Business'),
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
            'index' => Pages\ListClientTasks::route('/'),
            'create' => Pages\CreateClientTask::route('/create'),
            'edit' => Pages\EditClientTask::route('/{record}/edit'),
        ];
    }
}
