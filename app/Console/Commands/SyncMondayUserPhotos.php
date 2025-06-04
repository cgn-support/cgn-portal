<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\MondayApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncMondayUserPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monday:sync-user-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches and updates Monday.com user profile photos for users with a monday_user_id.';

    protected MondayApiService $mondayApiService;

    public function __construct(MondayApiService $mondayApiService)
    {
        parent::__construct();
        $this->mondayApiService = $mondayApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Monday.com user photo sync...');
        Log::info('Scheduled job: Starting SyncMondayUserPhotos.');

        // Fetch users who have a monday_user_id and are, for example, account managers or admins
        // Adjust the role check as needed for who should have their photos synced.
        $usersToSync = User::whereNotNull('monday_user_id')
            ->where(function ($query) {
                $query->whereHas('roles', fn($q) => $q->whereIn('name', ['account_manager', 'admin']))
                    ->orWhereNull('monday_photo_url'); // Also sync if photo is currently null, regardless of role (optional)
            })
            ->get();

        if ($usersToSync->isEmpty()) {
            $this->info('No users found with a Monday User ID requiring a photo sync.');
            Log::info('Scheduled job: SyncMondayUserPhotos - No users to sync.');
            return Command::SUCCESS;
        }

        $syncedCount = 0;
        $failedCount = 0;

        foreach ($usersToSync as $user) {
            $this->line("Processing user: {$user->name} (Monday ID: {$user->monday_user_id})");
            try {
                $photoUrl = $this->mondayApiService->getMondayUserProfilePhoto((string)$user->monday_user_id);

                if ($photoUrl) {
                    if ($user->monday_photo_url !== $photoUrl) {
                        $user->monday_photo_url = $photoUrl;
                        $user->save();
                        $this->info("-> Updated photo for {$user->name}.");
                        $syncedCount++;
                    } else {
                        $this->line("-> Photo for {$user->name} is already up to date.");
                    }
                } else {
                    // Optionally clear the photo if Monday.com returns null (e.g., photo removed)
                    // if ($user->monday_photo_url !== null) {
                    //     $user->monday_photo_url = null;
                    //     $user->save();
                    //     $this->warn("-> Cleared photo for {$user->name} as it's no longer available on Monday.com.");
                    // } else {
                    $this->warn("-> No photo found on Monday.com for {$user->name}.");
                    // }
                }
            } catch (\Exception $e) {
                $this->error("-> Failed to fetch photo for {$user->name}: " . $e->getMessage());
                Log::error("SyncMondayUserPhotos: Failed for user ID {$user->id}, Monday ID {$user->monday_user_id}: " . $e->getMessage());
                $failedCount++;
            }
            // Optional: Add a small delay to avoid hitting API rate limits if syncing many users
            // sleep(1); 
        }

        $this->info("Monday.com user photo sync completed. Synced: {$syncedCount}, Failed: {$failedCount}.");
        Log::info("Scheduled job: SyncMondayUserPhotos completed. Synced: {$syncedCount}, Failed: {$failedCount}.");
        return Command::SUCCESS;
    }
}
