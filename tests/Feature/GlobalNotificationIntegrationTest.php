<?php

namespace Tests\Feature;

use App\Models\GlobalNotification;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalNotificationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a client and user for testing
        $client = Client::factory()->create();
        $this->user = User::factory()->create(['client_id' => $client->id]);
    }

    public function test_global_notification_model_integration_with_user()
    {
        $notification = GlobalNotification::factory()->create([
            'title' => 'Test Integration',
            'content' => 'Testing model integration.',
            'type' => 'announcement',
        ]);

        // Test that user can dismiss notification
        $this->assertFalse($notification->isDismissedBy($this->user));
        
        $notification->dismissedByUsers()->attach($this->user->id, [
            'dismissed_at' => now(),
        ]);
        
        $this->assertTrue($notification->isDismissedBy($this->user));
    }

    public function test_visible_to_user_scope_works_correctly()
    {
        // Create visible notification
        $visibleNotification = GlobalNotification::factory()->create([
            'title' => 'Visible Notification',
            'is_active' => true,
        ]);
        
        // Create inactive notification
        GlobalNotification::factory()->inactive()->create(['title' => 'Inactive']);
        
        // Create expired notification
        GlobalNotification::factory()->expired()->create(['title' => 'Expired']);
        
        // Create dismissed notification
        $dismissedNotification = GlobalNotification::factory()->create(['title' => 'Dismissed']);
        $dismissedNotification->dismissedByUsers()->attach($this->user->id, [
            'dismissed_at' => now(),
        ]);

        $visibleNotifications = GlobalNotification::visibleToUser($this->user)->get();

        $this->assertCount(1, $visibleNotifications);
        $this->assertEquals('Visible Notification', $visibleNotifications->first()->title);
    }

    public function test_notification_type_attributes_work_correctly()
    {
        $types = ['announcement', 'feature', 'blog', 'podcast', 'video', 'general'];
        
        foreach ($types as $type) {
            $notification = GlobalNotification::factory()->create(['type' => $type]);
            
            // Test that type_icon and type_color attributes work
            $this->assertNotEmpty($notification->type_icon);
            $this->assertNotEmpty($notification->type_color);
            $this->assertStringStartsWith('heroicon-o-', $notification->type_icon);
        }
    }

    public function test_notification_scheduling_works_correctly()
    {
        // Past notification should be visible
        $pastNotification = GlobalNotification::factory()->create([
            'title' => 'Past Notification',
            'published_at' => now()->subHour(),
        ]);

        // Future notification should not be visible
        $futureNotification = GlobalNotification::factory()->create([
            'title' => 'Future Notification',
            'published_at' => now()->addHour(),
        ]);

        $visibleNotifications = GlobalNotification::visibleToUser($this->user)->get();
        
        $this->assertTrue($visibleNotifications->contains('title', 'Past Notification'));
        $this->assertFalse($visibleNotifications->contains('title', 'Future Notification'));
    }

    public function test_notification_expiration_works_correctly()
    {
        // Active notification should be visible
        $activeNotification = GlobalNotification::factory()->create([
            'title' => 'Active Notification',
            'expires_at' => now()->addDay(),
        ]);

        // Expired notification should not be visible
        $expiredNotification = GlobalNotification::factory()->create([
            'title' => 'Expired Notification',
            'expires_at' => now()->subDay(),
        ]);

        $visibleNotifications = GlobalNotification::visibleToUser($this->user)->get();
        
        $this->assertTrue($visibleNotifications->contains('title', 'Active Notification'));
        $this->assertFalse($visibleNotifications->contains('title', 'Expired Notification'));
    }

    public function test_multiple_users_can_dismiss_independently()
    {
        $notification = GlobalNotification::factory()->create(['title' => 'Multi-User Test']);
        
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // User 1 dismisses
        $notification->dismissedByUsers()->attach($user1->id, ['dismissed_at' => now()]);

        // Check visibility for each user
        $this->assertFalse($notification->isDismissedBy($user2));
        $this->assertTrue($notification->isDismissedBy($user1));
        
        // Verify scopes work correctly
        $visibleToUser1 = GlobalNotification::visibleToUser($user1)->get();
        $visibleToUser2 = GlobalNotification::visibleToUser($user2)->get();
        
        $this->assertCount(0, $visibleToUser1);
        $this->assertCount(1, $visibleToUser2);
    }

    public function test_notification_factory_states_work()
    {
        // Test different factory states
        $announcement = GlobalNotification::factory()->announcement()->create();
        $this->assertEquals('announcement', $announcement->type);
        $this->assertEquals('Important Company Announcement', $announcement->title);

        $feature = GlobalNotification::factory()->feature()->create();
        $this->assertEquals('feature', $feature->type);

        $blog = GlobalNotification::factory()->blog()->create();
        $this->assertEquals('blog', $blog->type);
        $this->assertNotNull($blog->link);

        $inactive = GlobalNotification::factory()->inactive()->create();
        $this->assertFalse($inactive->is_active);

        $withIcon = GlobalNotification::factory()->withCustomIcon()->create();
        $this->assertEquals('heroicon-o-star', $withIcon->icon);
    }

    public function test_livewire_component_integration()
    {
        $notification = GlobalNotification::factory()->create([
            'title' => 'Livewire Test',
            'content' => 'Testing Livewire integration.',
        ]);

        $this->actingAs($this->user);

        // Test Livewire component can load notifications
        \Livewire\Livewire::test(\App\Livewire\GlobalNotifications::class)
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1 && $notifications[0]['title'] === 'Livewire Test';
            });
    }

    public function test_notification_dismissal_through_livewire()
    {
        $notification = GlobalNotification::factory()->create(['title' => 'Dismiss Test']);

        $this->actingAs($this->user);

        // Test dismissal through Livewire
        \Livewire\Livewire::test(\App\Livewire\GlobalNotifications::class)
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 1;
            })
            ->call('dismissNotification', $notification->id)
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 0;
            });

        // Verify dismissal was recorded
        $this->assertTrue($notification->fresh()->isDismissedBy($this->user));
    }
}