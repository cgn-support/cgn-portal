<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Services\MondayApiService;
use App\Models\Project;
use App\Models\User; // For finding the Account Manager
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(), // Standard Delete Action
            Actions\ForceDeleteAction::make(), // Standard ForceDelete Action
            Actions\RestoreAction::make(), // Standard Restore Action
            Actions\Action::make('fetchMondayData')
                ->label('Fetch Monday Data')
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->action(function (Project $record, MondayApiService $mondayApiService) {
                    if (empty($record->monday_pulse_id)) {
                        Notification::make()->title('Missing Monday Pulse ID')->warning()->body('Please enter the Monday Pulse ID for this project first.')->send();
                        return;
                    }

                    $portfolioItemId = (string) $record->monday_pulse_id;
                    $updateData = [];

                    // Log::info("Attempting to fetch data for Portfolio Item ID: {$portfolioItemId} from Portfolio Board ID: {$mondayApiService->getPortfolioBoardId()}."); // Assuming a getter for portfolioBoardId or log it within service

                    // Define ALL column IDs from your Portfolio Board you want to fetch for general details
                    $generalColumnIdsToFetch = [
                        'portfolio_project_rag',      // Project Health (RAG)
                        'portfolio_project_doc',      // Project Status Summary (Doc)
                        'portfolio_project_scope',    // Project Description
                        'text_mkpxp60t',              // Domain (maps to project_url)
                        'dropdown_mkpxzd6j',          // Current Services
                        'dropdown_mkpxtdjv',          // Completed Services
                        'multiple_person_mkpxtspm',   // Account Manager (People column)
                        'multiple_person_mkpxkdc5',   // Specialist
                        'multiple_person_mkpxnxmt',   // Content Writer
                        'multiple_person_mkpxg4m4',   // Developer
                        'multiple_person_mkpxy34t',   // Copywriter
                        'multiple_person_mkpxjyzm',   // Designer
                        'text_mkrgjna6',              // Drive Folder ID (maps to google_drive_folder)
                        'file_mkrgzpv0',              // Client Logo
                        'text_mkrgday0',              // Slack Channel ID (maps to slack_channel)
                        'text_mkrgfr6x',              // Bright Local ID (maps to bright_local_url)
                        'text_mkrgcavn',              // Project Workbook ID (maps to google_sheet_id)
                        'text_mkrgzj64',              // WP Umbrella ID (maps to wp_umbrella_project_id)
                    ];
                    $generalColumnIdsToFetch = array_filter($generalColumnIdsToFetch, fn($id) => $id !== 'name');


                    try {
                        // --- 1. Fetch general column data from Portfolio Item ---
                        $itemData = $mondayApiService->getPortfolioItemDetails($portfolioItemId, $generalColumnIdsToFetch);

                        if ($itemData) {
                            if (isset($itemData['name'])) {
                                Log::info("Fetched Monday item name: {$itemData['name']} for Laravel project: {$record->name}. Not automatically updating Laravel project name.");
                            }

                            $columnValues = $itemData['column_values'] ?? [];
                            Log::info("Fetched general column_values for item {$portfolioItemId}:", $columnValues);

                            $extractFirstPersonId = function ($personColumnDataArray) {
                                if ($personColumnDataArray && isset($personColumnDataArray['value']) && is_string($personColumnDataArray['value'])) {
                                    $valueArray = json_decode($personColumnDataArray['value'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && isset($valueArray['personsAndTeams'][0]['id'])) {
                                        return (string) $valueArray['personsAndTeams'][0]['id'];
                                    }
                                }
                                return null;
                            };

                            // --- Account Manager Sync ---
                            $accountManagerMondayColumnId = 'multiple_person_mkpxtspm';
                            $amPortfolioData = $mondayApiService->getColumnDataById($columnValues, $accountManagerMondayColumnId);
                            $mondayAmUserId = $extractFirstPersonId($amPortfolioData);

                            if ($mondayAmUserId) {
                                $laravelAmUser = User::where('monday_user_id', $mondayAmUserId)
                                    ->whereHas('roles', fn($q) => $q->where('name', 'account_manager'))
                                    ->first();
                                if ($laravelAmUser) {
                                    $updateData['account_manager_id'] = $laravelAmUser->id;
                                    Log::info("Synced Account Manager: Found Laravel user ID {$laravelAmUser->id} for Monday User ID {$mondayAmUserId}");
                                } else {
                                    Log::warning("Could not find a Laravel user with role 'account_manager' and Monday User ID: {$mondayAmUserId}. Project AM not updated.");
                                    Notification::make()->title('AM Sync Issue')->warning()->body("Account Manager with Monday ID {$mondayAmUserId} not found in portal or lacks 'account_manager' role. Project AM not updated.")->send();
                                }
                            } else {
                                Log::info("No Account Manager assigned in Monday.com column '{$accountManagerMondayColumnId}' for Pulse ID {$portfolioItemId}.");
                            }

                            // --- Map other specific columns ---
                            // 1. Domain (text_mkpxp60t) -> project_url
                            $domainColumn = $mondayApiService->getColumnDataById($columnValues, 'text_mkpxp60t');
                            if ($domainColumn && isset($domainColumn['text']) && trim($domainColumn['text']) !== '') {
                                $updateData['project_url'] = trim($domainColumn['text']);
                            }

                            // 3. Current Services (dropdown_mkpxzd6j) -> current_services (array)
                            $currentServicesColumn = $mondayApiService->getColumnDataById($columnValues, 'dropdown_mkpxzd6j');
                            if ($currentServicesColumn && isset($currentServicesColumn['text']) && trim($currentServicesColumn['text']) !== '') {
                                $updateData['current_services'] = array_map('trim', explode(',', $currentServicesColumn['text']));
                            }

                            // 4. Completed Services (dropdown_mkpxtdjv) -> completed_services (array)
                            $completedServicesColumn = $mondayApiService->getColumnDataById($columnValues, 'dropdown_mkpxtdjv');
                            if ($completedServicesColumn && isset($completedServicesColumn['text']) && trim($completedServicesColumn['text']) !== '') {
                                $updateData['completed_services'] = array_map('trim', explode(',', $completedServicesColumn['text']));
                            }

                            // 5. Specialist (multiple_person_mkpxkdc5) -> specialist_monday_id
                            $updateData['specialist_monday_id'] = $extractFirstPersonId(
                                $mondayApiService->getColumnDataById($columnValues, 'multiple_person_mkpxkdc5')
                            );
                            // 6. Content Writer (multiple_person_mkpxnxmt) -> content_writer_monday_id
                            $updateData['content_writer_monday_id'] = $extractFirstPersonId(
                                $mondayApiService->getColumnDataById($columnValues, 'multiple_person_mkpxnxmt')
                            );
                            // 7. Developer (multiple_person_mkpxg4m4) -> developer_monday_id
                            $updateData['developer_monday_id'] = $extractFirstPersonId(
                                $mondayApiService->getColumnDataById($columnValues, 'multiple_person_mkpxg4m4')
                            );
                            // 8. Copywriter (multiple_person_mkpxy34t) -> copywriter_monday_id
                            $updateData['copywriter_monday_id'] = $extractFirstPersonId(
                                $mondayApiService->getColumnDataById($columnValues, 'multiple_person_mkpxy34t')
                            );
                            // 9. Designer (multiple_person_mkpxjyzm) -> designer_monday_id
                            $updateData['designer_monday_id'] = $extractFirstPersonId(
                                $mondayApiService->getColumnDataById($columnValues, 'multiple_person_mkpxjyzm')
                            );

                            // 10. Drive Folder ID (text_mkrgjna6) -> google_drive_folder
                            $driveFolderColumn = $mondayApiService->getColumnDataById($columnValues, 'text_mkrgjna6');
                            if ($driveFolderColumn && isset($driveFolderColumn['text']) && trim($driveFolderColumn['text']) !== '') {
                                $updateData['google_drive_folder'] = trim($driveFolderColumn['text']);
                            }

                            // 11. Client Logo (file_mkrgzpv0) -> client_logo
                            $clientLogoColumn = $mondayApiService->getColumnDataById($columnValues, 'file_mkrgzpv0');
                            if ($clientLogoColumn && isset($clientLogoColumn['text']) && trim($clientLogoColumn['text']) !== '') {
                                $updateData['client_logo'] = trim($clientLogoColumn['text']);
                            }

                            // 12. Slack Channel ID (text_mkrgday0) -> slack_channel
                            $slackChannelColumn = $mondayApiService->getColumnDataById($columnValues, 'text_mkrgday0');
                            if ($slackChannelColumn && isset($slackChannelColumn['text']) && trim($slackChannelColumn['text']) !== '') {
                                $updateData['slack_channel'] = trim($slackChannelColumn['text']);
                            }

                            // 13. Bright Local ID (text_mkrgfr6x) -> bright_local_url
                            $brightLocalColumn = $mondayApiService->getColumnDataById($columnValues, 'text_mkrgfr6x');
                            if ($brightLocalColumn && isset($brightLocalColumn['text']) && trim($brightLocalColumn['text']) !== '') {
                                $updateData['bright_local_url'] = trim($brightLocalColumn['text']);
                            }

                            // 14. Project Workbook ID (text_mkrgcavn) -> google_sheet_id
                            $workbookIdColumn = $mondayApiService->getColumnDataById($columnValues, 'text_mkrgcavn');
                            if ($workbookIdColumn && isset($workbookIdColumn['text']) && trim($workbookIdColumn['text']) !== '') {
                                $updateData['google_sheet_id'] = trim($workbookIdColumn['text']);
                            }

                            // 15. WP Umbrella ID (text_mkrgzj64) -> wp_umbrella_project_id
                            $wpUmbrellaColumn = $mondayApiService->getColumnDataById($columnValues, 'text_mkrgzj64');
                            if ($wpUmbrellaColumn && isset($wpUmbrellaColumn['text']) && trim($wpUmbrellaColumn['text']) !== '') {
                                $updateData['wp_umbrella_project_id'] = trim($wpUmbrellaColumn['text']);
                            }

                            $ragColumnData = $mondayApiService->getColumnDataById($columnValues, 'portfolio_project_rag');
                            if ($ragColumnData && isset($ragColumnData['text'])) {
                                $updateData['portfolio_project_rag'] = $ragColumnData['text'];
                            }

                            $docColumnData = $mondayApiService->getColumnDataById($columnValues, 'portfolio_project_doc');
                            if ($docColumnData) {
                                if (isset($docColumnData['value']) && is_string($docColumnData['value'])) {
                                    $docValueDecoded = json_decode($docColumnData['value'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && isset($docValueDecoded['files'][0]['linkToFile'])) {
                                        $updateData['portfolio_project_doc'] = $docValueDecoded['files'][0]['linkToFile'];
                                    } elseif (isset($docColumnData['text']) && trim($docColumnData['text']) !== '') {
                                        $updateData['portfolio_project_doc'] = $docColumnData['text'];
                                    }
                                } elseif (isset($docColumnData['text']) && trim($docColumnData['text']) !== '') {
                                    $updateData['portfolio_project_doc'] = $docColumnData['text'];
                                }
                            }

                            $scopeColumnData = $mondayApiService->getColumnDataById($columnValues, 'portfolio_project_scope');
                            if ($scopeColumnData && isset($scopeColumnData['text'])) {
                                $updateData['portfolio_project_scope'] = $scopeColumnData['text'];
                            }
                        } else {
                            Notification::make()->title('Monday Data Not Found')->warning()->body("General details not found for Pulse ID: {$portfolioItemId}. Verify Pulse ID and Portfolio Board ID configuration.")->send();
                        }

                        // --- 2. Fetch the linked Project Board ID specifically ---
                        $mirrorColumnForBoardId = 'portfolio_project_progress';
                        $linkedBoardId = $mondayApiService->getLinkedBoardIdFromMirrorColumn($portfolioItemId, $mirrorColumnForBoardId);

                        if ($linkedBoardId) {
                            $updateData['monday_board_id'] = $linkedBoardId;
                        } else {
                            Notification::make()->title('Linked Board ID Missing')->danger()->body("Could not extract the linked Project Board ID from Monday.com for Pulse ID {$portfolioItemId}. Check the '{$mirrorColumnForBoardId}' column setup on Monday.com, and verify the Portfolio Board ID configuration in your application.")->send();
                            Log::warning("Failed to retrieve linked Monday Board ID for Pulse ID {$portfolioItemId}.");
                        }

                        // --- 3. Update Laravel Project Record ---
                        $updateData = array_filter($updateData, fn($value) => !is_null($value) && $value !== '');


                        if (!empty($updateData)) {
                            $record->update($updateData);
                            Notification::make()->title('Monday Data Fetched')->success()->body('Project details have been updated from Monday.com.')->send();
                            return redirect(ProjectResource::getUrl('edit', ['record' => $record]));
                        } else {
                            Notification::make()->title('No New Data to Update')->info()->body('Fetched data from Monday.com, but no new information was found or needed to update the project fields.')->send();
                        }
                    } catch (\Exception $e) {
                        Log::error("Filament Fetch Monday Data Action Error for Project ID {$record->id}: " . $e->getMessage(), ['trace' => Str::limit($e->getTraceAsString(), 1000)]);
                        Notification::make()->title('Error Fetching Monday Data')->danger()->body('An error occurred: ' . Str::limit($e->getMessage(), 150))->send();
                    }
                })
                ->requiresConfirmation()
        ];
    }

    // You can add other page methods like mutateFormDataBeforeSave, etc., if needed.
    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     // Example: if you needed to do something before the main form save
    //     return $data;
    // }
}
