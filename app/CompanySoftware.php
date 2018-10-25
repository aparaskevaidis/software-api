<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySoftware extends Model
{
    protected $table = 'company_softwares';

    public function software()
    {
        return $this->belongsTo(Software::class,'software_id', 'id');
    }

    public function licences()
    {
        return $this->hasMany(Licences::class, 'software_id', 'id');
    }
}
