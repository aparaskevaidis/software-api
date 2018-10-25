<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySoftware extends Model
{
    public function software()
    {
        return $this->belongsTo(Software::class);
    }

    public function licence()
    {
        return $this->hasMany(Licences::class);
    }
}
