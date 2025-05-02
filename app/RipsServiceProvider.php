<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsServiceProvider extends Model
{
    protected $fillable = [
        'company_id',
        'document_type_id',
        'name',
        'email',
        'code',
        'document_number',
    ];

    public function companies()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    // type_document_identifications
    public function documentType()
    {
        return $this->belongsTo(TypeDocumentIdentification::class, 'document_type_id');
    }

    public function scopeFilter($query, $search = null) {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('document_number', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('code', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            });
        }
        return $query;
    }
}
