<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsConsultationPurpose extends Model
{
    protected $table = 'rips_consultation_purposes';

    protected $fillable = [
        'code',
        'name',
    ];
}
