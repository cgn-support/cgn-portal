<?php

namespace Tests\Feature;

use App\Livewire\GlobalNotifications;
use App\Models\GlobalNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GlobalNotificationsLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_component_renders_successfully()
    {
        Livewire::test(GlobalNotifications::class)
            ->assertStatus(200);
    }

    public function test_component_loads_visible_notifications_on_mount()
    {
        // Create visible notifications
        $visibleNotification1 = GlobalNotification::factory()->create([
            'title' => 'Visible Notification 1',
            'is_active' => true,
        ]);
        
        $visibleNotification2 = GlobalNotification::factory()->create([
            'title' => 'Visible Notification 2',
            'is_active' => true,
        ]);

        // Create notifications that should not be visible
        GlobalNotification::factory()->inactive()->create(['title' => 'Inactive Notification']);
        GlobalNotification::factory()->expired()->create(['title' => 'Expired Notification']);

        Livewire::test(GlobalNotifications::class)
            ->assertSet('notifications', function ($notifications) use ($visibleNotification1, $visibleNotification2) {
                return count($notifications) === 2 &&
                       collect($notifications)->pluck('id')->contains($visibleNotification1->id) &&
                       collect($notifications)->pluck('id')->contains($visibleNotification2->id);
            });
    }

    public function test_component_excludes_dismissed_notifications()
    {
        $notification1 = GlobalNotification::factory()->create(['title' => 'Not Dismissed']);
        $notification2 = GlobalNotification::factory()->create(['title' => 'Dismissed']);

        // Dismiss notification2 for the current user
        $notification2->dismissedByUsers()->attach($this->user->id, [
            'dismissed_at' => now(),
        ]);

        Livewire::test(GlobalNotifications::class)
            ->assertSet('notifications', function ($notifications) use ($notification1) {
                return count($notifications) === 1 &&
                       $notifications[0]['id'] === $notification1->id;
            });
    }

    public function test_dismiss_notification_method_works()
    {
        $notification = GlobalNotification::factory()->create();

        $this->assertFalse($notification->isDismissedBy($this->user));

        Livewire::test(GlobalNotifications::class)
            ->call('dismissNotification', $notification->id);

        $notification->refresh();
        $this->assertTrue($notification->isDismissedBy($this->user));
    }

    public function test_dismiss_notification_removes_from_list()
    {
        $notification1 = GlobalNotification::factory()->create(['title' => 'Keep This']);
        $notification2 = GlobalNotification::factory()->create(['title' => 'Dismiss This']);

        Livewire::test(GlobalNotifications::class)
            ->assertSet('notifications', function ($notifications) {
                return count($notifications) === 2;
            })
            ->call('dismissNotification', $notification2->id)
            ->assertSet('notifications', function ($notifications) use ($notification1) {
                return count($notifications) === 1 &&
                       $notifications[0]['id'] === $notification1->id;
            });
    }

    public function test_dismiss_notification_with_invalid_id_does_nothing()
    {
        $notification = GlobalNotification::factory()->create();

        Livewire::test(GlobalNotifications::class)
            ->call('dismissNotification', 99999); // Non-existent ID

        $notification->refresh();
        $this->assertFalse($notification->isDismissedBy($this->user));
    }

    public function test_notifications_are_ordered_by_created_at_desc()
    {
        $oldNotification = GlobalNotification::factory()->create([
            'title' => 'Old Notification',
            'created_at' => now()->subDays(3),
        ]);
        
        $newNotification = GlobalNotification::factory()->create([
            'title' => 'New Notification',
            'created_at' => now()->subDay(),
        ]);

        Livewire::test(GlobalNotifications::class)
            ->assertSet('notifications', function ($notifications) use ($newNotification, $oldNotification) {
                return $notifications[0]['id'] === $newNotification->id &&
                       $notifications[1]['id'] === $oldNotification->id;
            });
    }

    public function test_component_shows_empty_state_when_no_notifications()
    {
        Livewire::test(GlobalNotifications::class)
            ->assertSet('notifications', [])
            ->assertSee('No new notifications');
    }

    public function test_component_displays_notification_content()
    {
        $notification = GlobalNotification::factory()->create([
            'title' => 'Test Notification Title',
            'content' => 'Test notification content',
            'type' => 'announcement',
        ]);

        Livewire::test(GlobalNotifications::class)
            ->assertSee('Test Notification Title')
            ->assertSee('Test notification content')
            ->assertSee('Announcement');
    }

    public function test_component_displays_notification_link_when_present()
    {
        $notification = GlobalNotification::factory()->create([
            'title' => 'Test Notification',
            'link' => 'https://example.com',
        ]);

        Livewire::test(GlobalNotifications::class)
            ->assertSee('https://example.com')
            ->assertSee('Learn more');
    }

    public function test_component_does_not_display_link_when_not_present()
    {
        $notification = GlobalNotification::factory()->create([
            'title' => 'Test Notification',
            'link' => null,
        ]);

        Livewire::test(GlobalNotifications::class)
            ->assertDontSee('Learn more');
    }

    public function test_dismiss_button_is_present_for_each_notification()
    {
        $notification = GlobalNotification::factory()->create();

        Livewire::test(GlobalNotifications::class)
            ->assertSee('wire:click="dismissNotification(' . $notification->id . ')"', false);
    }

    public function test_different_notification_types_display_correctly()
    {
        $types = ['announcement', 'feature', 'blog', 'podcast', 'video', 'general'];
        
        foreach ($types as $type) {
            GlobalNotification::factory()->create([
                'type' => $type,
                'title' => ucfirst($type) . ' Notification',
            ]);
        }

        $component = Livewire::test(GlobalNotifications::class);
        
        $component->assertSee('Announcement')
                  ->assertSee('New Feature')
                  ->assertSee('Blog Post')
                  ->assertSee('Podcast')
                  ->assertSee('Video')
                  ->assertSee('General');
    }

    public function test_load_notifications_method_refreshes_notifications()
    {
        $component = Livewire::test(GlobalNotifications::class);
        
        // Initially no notifications
        $component->assertSet('notifications', []);

        // Create a notification
        $notification = GlobalNotification::factory()->create();

        // Call loadNotifications manually
        $component->call('loadNotifications')
                  ->assertSet('notifications', function ($notifications) use ($notification) {
                      return count($notifications) === 1 &&
                             $notifications[0]['id'] === $notification->id;
                  });
    }
}