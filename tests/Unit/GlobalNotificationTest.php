<?php

namespace Tests\Unit;

use App\Models\GlobalNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class GlobalNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_global_notification()
    {
        $notification = GlobalNotification::factory()->create([
            'title' => 'Test Notification',
            'content' => 'This is a test notification.',
            'type' => 'announcement',
        ]);

        $this->assertDatabaseHas('global_notifications', [
            'title' => 'Test Notification',
            'content' => 'This is a test notification.',
            'type' => 'announcement',
        ]);
    }

    public function test_it_has_correct_fillable_attributes()
    {
        $notification = new GlobalNotification();
        
        $expectedFillable = [
            'title',
            'content',
            'type',
            'icon',
            'link',
            'is_active',
            'published_at',
            'expires_at',
        ];

        $this->assertEquals($expectedFillable, $notification->getFillable());
    }

    public function test_it_casts_attributes_correctly()
    {
        $notification = GlobalNotification::factory()->create([
            'is_active' => 1,
            'published_at' => '2024-01-01 12:00:00',
            'expires_at' => '2024-12-31 23:59:59',
        ]);

        $this->assertIsBool($notification->is_active);
        $this->assertInstanceOf(Carbon::class, $notification->published_at);
        $this->assertInstanceOf(Carbon::class, $notification->expires_at);
    }

    public function test_it_can_be_dismissed_by_user()
    {
        $notification = GlobalNotification::factory()->create();
        $user = User::factory()->create();

        $notification->dismissedByUsers()->attach($user->id, [
            'dismissed_at' => now(),
        ]);

        $this->assertTrue($notification->isDismissedBy($user));
    }

    public function test_it_returns_false_when_not_dismissed_by_user()
    {
        $notification = GlobalNotification::factory()->create();
        $user = User::factory()->create();

        $this->assertFalse($notification->isDismissedBy($user));
    }

    public function test_active_scope_returns_only_active_notifications()
    {
        GlobalNotification::factory()->create(['is_active' => true]);
        GlobalNotification::factory()->inactive()->create();

        $activeNotifications = GlobalNotification::active()->get();

        $this->assertCount(1, $activeNotifications);
        $this->assertTrue($activeNotifications->first()->is_active);
    }

    public function test_published_scope_returns_published_notifications()
    {
        // Notification without published_at (should be included)
        GlobalNotification::factory()->create(['published_at' => null]);
        
        // Notification published in the past (should be included)
        GlobalNotification::factory()->create(['published_at' => now()->subDay()]);
        
        // Notification scheduled for future (should not be included)
        GlobalNotification::factory()->scheduled()->create();

        $publishedNotifications = GlobalNotification::published()->get();

        $this->assertCount(2, $publishedNotifications);
    }

    public function test_not_expired_scope_returns_non_expired_notifications()
    {
        // Notification without expiry (should be included)
        GlobalNotification::factory()->create(['expires_at' => null]);
        
        // Notification expiring in future (should be included)
        GlobalNotification::factory()->create(['expires_at' => now()->addDay()]);
        
        // Expired notification (should not be included)
        GlobalNotification::factory()->expired()->create();

        $notExpiredNotifications = GlobalNotification::notExpired()->get();

        $this->assertCount(2, $notExpiredNotifications);
    }

    public function test_visible_to_user_scope_returns_correct_notifications()
    {
        $user = User::factory()->create();
        
        // Visible notification
        $visibleNotification = GlobalNotification::factory()->create([
            'is_active' => true,
            'published_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);
        
        // Inactive notification
        GlobalNotification::factory()->inactive()->create();
        
        // Expired notification
        GlobalNotification::factory()->expired()->create();
        
        // Dismissed notification
        $dismissedNotification = GlobalNotification::factory()->create();
        $dismissedNotification->dismissedByUsers()->attach($user->id, [
            'dismissed_at' => now(),
        ]);

        $visibleNotifications = GlobalNotification::visibleToUser($user)->get();

        $this->assertCount(1, $visibleNotifications);
        $this->assertEquals($visibleNotification->id, $visibleNotifications->first()->id);
    }

    public function test_get_type_icon_attribute_returns_correct_icons()
    {
        $testCases = [
            'announcement' => 'heroicon-o-megaphone',
            'feature' => 'heroicon-o-sparkles',
            'blog' => 'heroicon-o-document-text',
            'podcast' => 'heroicon-o-microphone',
            'video' => 'heroicon-o-play-circle',
            'general' => 'heroicon-o-bell',
        ];

        foreach ($testCases as $type => $expectedIcon) {
            $notification = GlobalNotification::factory()->create(['type' => $type]);
            $this->assertEquals($expectedIcon, $notification->type_icon);
        }
    }

    public function test_get_type_icon_attribute_returns_custom_icon_when_set()
    {
        $notification = GlobalNotification::factory()->withCustomIcon()->create();
        
        $this->assertEquals('heroicon-o-star', $notification->type_icon);
    }

    public function test_get_type_color_attribute_returns_correct_colors()
    {
        $testCases = [
            'announcement' => 'blue',
            'feature' => 'green',
            'blog' => 'purple',
            'podcast' => 'orange',
            'video' => 'red',
            'general' => 'gray',
        ];

        foreach ($testCases as $type => $expectedColor) {
            $notification = GlobalNotification::factory()->create(['type' => $type]);
            $this->assertEquals($expectedColor, $notification->type_color);
        }
    }

    public function test_dismissed_by_users_relationship()
    {
        $notification = GlobalNotification::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $notification->dismissedByUsers()->attach([
            $user1->id => ['dismissed_at' => now()],
            $user2->id => ['dismissed_at' => now()->subHour()],
        ]);

        $this->assertCount(2, $notification->dismissedByUsers);
        $this->assertTrue($notification->dismissedByUsers->contains($user1));
        $this->assertTrue($notification->dismissedByUsers->contains($user2));
    }
}