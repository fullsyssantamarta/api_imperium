<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsCareModality extends Model
{
    protected $fillable = [
        'id',
        'name',
        'code',
    ];
}
