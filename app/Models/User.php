<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, LogsActivity, \Spatie\Permission\Traits\HasRoles;

    public static $createMemberProfile = true;

    protected static function booted()
    {
        static::created(function ($user) {
            if (self::$createMemberProfile) {
                $exists = Member::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->exists();

                if (!$exists) {
                    Member::create([
                        'user_id' => $user->id,
                        'full_name' => $user->name,
                        'email' => $user->email,
                        'status' => 'active',
                    ]);
                }
            }
        });

        static::updated(function ($user) {
            if ($user->wasChanged(['name', 'email'])) {
                $member = $user->member;
                if ($member) {
                    $member->update([
                        'full_name' => $user->name,
                        'email' => $user->email,
                    ]);
                }
            }
        });
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['name', 'email']);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_viewed_birthdays_at',
        'last_viewed_announcements_at',
        'last_viewed_roster_at',
        'last_viewed_projects_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_viewed_birthdays_at' => 'datetime',
            'last_viewed_announcements_at' => 'datetime',
            'last_viewed_roster_at' => 'datetime',
            'last_viewed_projects_at' => 'datetime',
        ];
    }
    public function member()
    {
        return $this->hasOne(Member::class);
    }
}
