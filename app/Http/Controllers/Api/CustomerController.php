<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Customer;
use Illuminate\Http\Request;
use App\Custom\GetAdquirerRequest;

class CustomerController extends Controller
{
    public function records()
    {
        try {
            $companyId = auth()->user()->company->id;

            $customers = Customer::Where('companies_id', $companyId)
                            ->select('identification_number', 'dv', 'name', 'phone', 'address', 'email','companies_id')
                            ->paginate(20);
            return response()->json([
                'success' => true,
                'data' => $customers
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'identification_number' => 'required|string|max:20|unique:customers',
                'dv' => 'nullable|string|max:1',
                'name' => 'required|string|max:500',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'email' => 'nullable|email|max:255',
                'password' => 'required|string|min:6',
                'companies_id' => 'required|exists:companies,id'
            ]);

            $validatedData['password'] = bcrypt($validatedData['password']);
            $customer = Customer::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => [
                    'customer' => [
                        'name' => $customer->name,
                        'email' => $customer->email
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAcquirer($document_type_identification_id, $document_number)
    {
        $response = $this->createXML($document_type_identification_id, $document_number);

        try {
            if (isset($response->Envelope->Body->Fault)) {
                $fault = $response->Envelope->Body->Fault;
                $faultString = isset($fault->Reason->Text) ? (string)$fault->Reason->Text['_value'] : 'Unknown error';
                $faultCode = isset($fault->Code->Value) ? (string)$fault->Code->Value : 'Unknown code';

                return response()->json([
                    'success' => false,
                    'message' => 'Dian Error: ' . $faultString,
                    'code' => $faultCode
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing Dian response',
                'error' => $e->getMessage()
            ], 500);
        }
        $status = $response->Envelope->Body->GetAcquirerResponse->GetAcquirerResult->StatusCode;
        $message = $response->Envelope->Body->GetAcquirerResponse->GetAcquirerResult->Message;
        if($status === '404') {
            return [
                'success' => false,
                'message' => $message,
                'status' => $status
            ];
        }
        return [
            'success' => true,
            'message' => $message,
            'ResponseDian' => $response->Envelope->Body,
            'status' => $status
        ];
    }

    protected function createXML($document_type_identification_id, $document_number)
    {
        $company = auth()->user()->company;
        $getAdquirerRequest = new GetAdquirerRequest($company->certificate->path, $company->certificate->password);
        $getAdquirerRequest->identificationType = $document_type_identification_id;
        $getAdquirerRequest->identificationNumber = $document_number;
        $getAdquirerRequest->To = $company->software->url;
        $respuestadian = $getAdquirerRequest->signToSend()->getResponseToObject();

        return $respuestadian;
    }
}
