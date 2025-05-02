<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsPatient extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'last_name',
        'type_document_identification_id',
        'document_number',
        'rips_user_type_id',
        'birth_date',
        'rips_gender_id',
        'country_code',
        'municipality_id',
        'rips_zone_id',
        'incapacity',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function typeDocumentIdentification()
    {
        return $this->belongsTo(TypeDocumentIdentification::class, 'type_document_identification_id');
    }

    public function ripsUserType()
    {
        return $this->belongsTo(RipsUserType::class);
    }

    public function ripsGender()
    {
        return $this->belongsTo(RipsGender::class);
    }

    public function ripsZone()
    {
        return $this->belongsTo(RipsZone::class);
    }

    public function appointments()
    {
        return $this->hasMany(RipsAppointment::class);
    }

    public function scopeFilter($query, $search)
    {
        if ($search) {
            return $query->where('document_number', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%");
        }
        return $query;
    }
}
