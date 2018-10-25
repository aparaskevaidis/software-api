<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Licences extends Model
{
    public function companySoftware()
    {
        return $this->belongsTo(CompanySoftware::class);
    }
}
