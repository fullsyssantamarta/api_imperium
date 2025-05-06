<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsService extends Model
{
    protected $table = 'rips_services';


    protected $fillable = [
        'code',
        'name',
        'description',
        'is_enabled'
    ];
}
