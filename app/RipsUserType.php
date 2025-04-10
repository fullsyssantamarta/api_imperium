<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsUserType extends Model
{
    protected $table = 'rips_user_types';

    protected $fillable = [
        'code',
        'name',
    ];
}
