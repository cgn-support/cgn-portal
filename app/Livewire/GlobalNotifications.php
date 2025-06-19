<?php

namespace App\Livewire;

use App\Models\GlobalNotification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GlobalNotifications extends Component
{
    public $notifications = [];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = GlobalNotification::visibleToUser(Auth::user())
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function dismissNotification($notificationId)
    {
        $notification = GlobalNotification::find($notificationId);
        
        if ($notification) {
            $notification->dismissedByUsers()->attach(Auth::id(), [
                'dismissed_at' => now(),
            ]);
            
            $this->loadNotifications();
        }
    }

    public function render()
    {
        return view('livewire.global-notifications');
    }
}