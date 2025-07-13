<?php

namespace App\Models;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roomtype extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'roomtype',
        'capacity',
        'price',
        'description',
        'image',
        'remove_status',
    ];

    public function user()
    {

        return $this->belongsTo(User::class);

    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'roomtype_id');
    }
}
