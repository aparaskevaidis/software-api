<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    protected $table = 'software';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function companySoftware()
    {
        return $this->hasOne(CompanySoftware::class);
    }
}
