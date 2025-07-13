<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Room;
use App\Models\Roomtype;
use Laravel\Sanctum\HasApiTokens;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
        'username',
        'password',
        'logo',
        'profile_image',
        'hotel_name',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function roomtypes()
    {
        return $this->hasMany(Roomtype::class);
    }

    public function booking()
    {
        return $this->hasMany(BookingTransaction::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
}
