<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsMainDiagnosisType extends Model
{
    protected $table = 'rips_main_diagnosis_types';

    protected $fillable = [
        'code',
        'name',
    ];
}
