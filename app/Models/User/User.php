<?php

namespace App\Models\User;

use App\Models\ImageUpload;
use App\Models\Notification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail {
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'rank_id', 'theme',
        'is_banned', 'ban_reason', 'banned_at', 'receive_admin_notifs',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'banned_at'         => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'rank',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_banned'            => 0,
        'theme'                => 'dark',
        'notifications_unread' => 0,
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user's rank data.
     */
    public function rank() {
        return $this->belongsTo(Rank::class);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's images.
     */
    public function images() {
        return $this->hasMany(ImageUpload::class);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Checks if the user has an admin rank.
     *
     * @return bool
     */
    public function getIsAdminAttribute() {
        if ($this->is_banned) {
            return false;
        }

        return $this->rank->isAdmin;
    }

    /**
     * Checks if the user has an admin rank.
     *
     * @return bool
     */
    public function getIsModAttribute() {
        if ($this->is_banned) {
            return false;
        }

        return $this->rank->isMod;
    }

    /**
     * Displays the user's name, linked to their profile page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return ($this->is_banned ? '<strike>' : '').'<a href="'.$this->adminUrl.'" class="display-user">'.$this->name.'</a>'.($this->is_banned ? '</strike>' : '');
    }

    /**
     * Gets the URL for editing the user in the admin panel.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/users/'.$this->name.'/edit');
    }
}
