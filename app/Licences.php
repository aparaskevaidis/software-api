<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Licences extends Model
{
    protected $table = 'licences';

    public function companySoftware()
    {
        return $this->belongsTo(CompanySoftware::class, 'id', 'software_id');
    }
}
