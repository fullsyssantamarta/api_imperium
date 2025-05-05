<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsCie extends Model
{
    protected $table = 'rips_cies';

    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'is_enabled',
        'extra',
        'created_at',
        'updated_at'
    ];

    public function scopeFilter($query, $search)
    {
        if ($search) {
            $query->where('code', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%");
        }

        return $query;
    }
}
