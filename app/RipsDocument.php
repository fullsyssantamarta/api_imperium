<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsDocument extends Model
{
    protected $fillable = [
        'company_id',
        'appointment_id',
        'invoice_number',
        'note_type',
        'note_number',
        'xml_filename',
        'services',
        'request_api',
        'response_api'
    ];

    protected $casts = [
        'services' => 'json',
        'request_api' => 'json',
        'response_api' => 'json',
    ];

    public function appointment()
    {
        return $this->belongsTo(RipsAppointment::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
