<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RipsAppointment extends Model
{
    protected $fillable = [
        'company_id',
        'patient_id',
        'user_id',
        'time',
        'date'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function patient()
    {
        return $this->belongsTo(RipsPatient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(RipsDocument::class, 'appointment_id');
    }

    public function scopeFilter($query, $search, $month = null)
    {
        // Filter by month
        if ($month) {
            $query->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$month]);
        }

        if ($search) {
            $query->orWhere('date', 'like', "%$search%")
            ->orWhere('time', 'like', "%$search%");
        }

        // Filter by search terms
        // if ($search) {
        //     $query->where(function($q) use ($search) {
        //         $q->whereHas('patient', function($query) use ($search) {
        //             $query->where('name', 'like', "%$search%")
        //                   ->orWhere('document_number', 'like', "%$search%");
        //         })
        //         ->orWhereHas('serviceProvider', function($query) use ($search) {
        //             $query->where('name', 'like', "%$search%")
        //                   ->orWhere('code', 'like', "%$search%");
        //         })
        //         ->orWhere('date', 'like', "%$search%")
        //         ->orWhere('time', 'like', "%$search%");
        //     });
        // }

        return $query;
    }
}
