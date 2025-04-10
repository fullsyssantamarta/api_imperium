<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsProcedureCode extends Model
{
    protected $table = 'rips_procedure_codes';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_enabled'
    ];
}
