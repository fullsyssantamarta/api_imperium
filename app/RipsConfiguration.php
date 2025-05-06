<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsConfiguration extends Model
{
    protected $table = 'rips_configurations';

    protected $fillable = [
        'company_id',
        'type_document_identification_id',
        'url',
        'number_identification',
        'password',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function typeDocumentIdentification()
    {
        return $this->belongsTo(TypeDocumentIdentification::class);
    }
}
