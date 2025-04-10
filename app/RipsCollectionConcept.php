<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsCollectionConcept extends Model
{
    protected $table = 'rips_collection_concepts';

    protected $fillable = [
        'code',
        'name',
        'description',
    ];
}
