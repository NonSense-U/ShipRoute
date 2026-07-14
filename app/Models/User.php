<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'phone_verified_at',
        'id_card_number',
        'password',
        'fcm_token',
    ];

    protected $appends = [
        'rating_info',
    ];

    protected string $guard_name = 'api';
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfileAttribute()
    {
        if ($this->hasRole('merchant')) {
            return $this->merchant;
        }

        if ($this->hasRole('driver')) {
            return $this->driver;
        }

        return null;
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }


    public function getRatingInfoAttribute()
    {
        $info = [
            'average_rating' => $this->ratingsReceived()->avg('rating'),
            'total_ratings' => $this->ratingsReceived()->count(),
        ];
        return $info;
    }

    //! Firebase
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
}
