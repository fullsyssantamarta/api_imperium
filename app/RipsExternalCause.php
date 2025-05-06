<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsExternalCause extends Model
{
    protected $table = 'rips_external_causes';

    protected $fillable = [
        'code',
        'name',
    ];
}
