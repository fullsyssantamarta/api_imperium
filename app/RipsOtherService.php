<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsOtherService extends Model
{
    protected $table = 'rips_other_services';

    protected $fillable = [
        'code',
        'name',
    ];
}
