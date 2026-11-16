<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One room inspection record (table: room_checks).
 */
class RoomCheck extends Model
{
    protected $fillable = [
        'room_id',
        'student_id',
        'student_name',
    ];
}
