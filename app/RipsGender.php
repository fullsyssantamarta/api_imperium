<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsGender extends Model
{
    protected $table = 'rips_genders';

    protected $fillable = [
        'code',
        'name',
    ];
}
