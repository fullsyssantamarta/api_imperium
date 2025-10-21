<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TypeOvertimeSurcharge;

class HealthUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_code', 'health_type_document_identification_id', 'identification_number', 'surname', 'second_surname',
        'first_name', 'middle_name', 'health_type_user_id', 'health_contracting_payment_method_id', 'health_coverage_id',
        'autorization_numbers', 'mipres', 'mipres_delivery', 'contract_number', 'policy_number', 'co_payment', 'moderating_fee',
        'recovery_fee', 'shared_payment', 'advance_payment'
    ];

    /**
    * Get the health type document identification belongs to
    */
    public function health_type_document_identification() {
        return $this->belongsTo(HealthTypeDocumentIdentification::class);
    }

    /**
    * Get the health type user belongs to
    */
    public function health_type_user() {
        return $this->belongsTo(HealthTypeUser::class);
    }

    /**
    * Get the health contracting payment method belongs to
    */
    public function health_contracting_payment_method() {
        return $this->belongsTo(HealthContractingPaymentMethod::class);
    }

    /**
    * Get the health coverage belongs to
    */
    public function health_coverage() {
        return $this->belongsTo(HealthCoverage::class);
    }
}
