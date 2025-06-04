<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- Add this line
use Illuminate\Database\Eloquent\Relations\HasMany; // <--- Add this line
use App\Models\Project; // <--- Add this line

class User extends Authenticatable // implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes; // <--- Add SoftDeletes here

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'client_id',
        'monday_user_id',
        'monday_photo_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'deleted_at' => 'datetime', // Optional: good practice to cast deleted_at
    ];

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isAccountManager()
    {
        return $this->hasRole('account_manager');
    }

    public function isClientUser()
    {
        return $this->hasRole('client_user');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function companyProjects()
    {
        if ($this->hasRole('client_user') && $this->client) { // Ensure user is a client user and has a client
            return $this->client->projects(); // Returns the relationship builder from Client model
        }
        // Return an empty query builder if not applicable or no client
        return Project::query()->whereRaw('1 = 0');
    }

    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'account_manager_id');
    }

    public function assignedClientProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'user_id');
    }
}
