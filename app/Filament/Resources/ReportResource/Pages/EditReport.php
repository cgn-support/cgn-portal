<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('publish')
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
        ];
    }
}
