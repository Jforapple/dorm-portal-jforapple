<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = ['title', 'dorm_id', 'date', 'session'];

    /**
     * Get the dorm that owns the worship event.
     */
    public function dorm(): BelongsTo
    {
        return $this->belongsTo(Dorm::class);
    }

    /**
     * Cast date to a Carbon instance.
     */
    protected function casts(): array
    {
        return ['date' => 'date'];
    }
}
