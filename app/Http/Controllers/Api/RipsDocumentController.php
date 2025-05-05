<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\RipsDocument;

class RipsDocumentController extends Controller
{
    /*
        'company_id',
        'appointment_id',
        'invoice_number',
        'note_type',
        'note_number',
        'xml_filename',
        'services',
    */
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'services' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $company =$this->getCompanyId();
            $request->merge(['company_id' => $company->id]);
            $document = RipsDocument::create([
                "company_id" => $company->id,
                "appointment_id" => $request->appointment_id,
                "invoice_number" => null,
                "note_type" => $request->note_type,
                "note_number" => $request->note_number,
                "xml_filename" => null,
                "services" => $request->services
            ]);
            return response()->json([
                'success' => true,
                'data' => $document,
                'message' => 'document created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating document',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
