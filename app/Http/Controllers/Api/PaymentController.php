<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\PaymentForm;
use App\PaymentMethod;

class PaymentController extends Controller
{
    public function getPaymentMethods()
    {
        $payment_methods = PaymentMethod::all();
        return compact('payment_methods');
    }

    public function getPaymentForms()
    {
        $payment_forms = PaymentForm::all();
        return compact('payment_forms');
    }
}
