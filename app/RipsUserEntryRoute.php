<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsUserEntryRoute extends Model
{
    protected $table = 'rips_user_entry_routes';


    protected $fillable = [
        'code',
        'name',
    ];
}
