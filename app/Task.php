<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    const NOT_COMPLETED = 0;
    const IS_COMPLETED = 1;

    protected $fillable = ['text', 'is_completed', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
