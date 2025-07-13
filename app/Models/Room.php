<?php

namespace App\Models;

use App\Models\User;
use App\Models\Roomtype;
use App\Models\RoomBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_no',
        'roomtype_id',
        'status',
        'remove_status',
        'room_status',
        'extra_bed'
    ];

    public function user()
    {

        return $this->belongsTo(User::class);

    }

    public function roomtypes()
    {

        return $this->belongsTo(Roomtype::class, 'roomtype_id');

    }
    

    public function roomBoooking()
    {
        return $this->hasMany(RoomBooking::class);
    }
}
