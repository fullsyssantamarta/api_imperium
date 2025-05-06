<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsZone extends Model
{
    protected $table = 'rips_zones';

    protected $fillable = [
        'code',
        'name',
    ];
}
