<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsServiceGroup extends Model
{
    protected $table = 'rips_service_groups';

    protected $fillable = [
        'code',
        'name',
    ];
}
