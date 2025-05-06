<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\HealthTypeDocumentIdentification;
use App\RipsCareModality;
use App\RipsServiceGroup;
use App\RipsMainDiagnosisType;
use App\RipsCollectionConcept;
use App\RipsProcedureCode;
use App\RipsService;
use App\RipsConsultationPurpose;
use App\RipsExternalCause;
use App\RipsCie;

class RipsServiceController extends Controller
{
    public function getTables() {
        try {
            $documentTypes = HealthTypeDocumentIdentification::select('code', 'name')->get()->map(function ($item) {
                $item->code = trim($item->code);
                return $item;
            });
            $care_modalities = RipsCareModality::select('code', 'name')->get();
            $service_groups = RipsServiceGroup::select('code', 'name')->get();
            $main_diagnosis_types = RipsMainDiagnosisType::select('code', 'name')->get();
            $collection_concepts = RipsCollectionConcept::select('code', 'name')->get();
            $service_codes = RipsService::select('code', 'name', 'description')->get();
            $consultation_purposes = RipsConsultationPurpose::select('code', 'name')->get();
            $external_causes = RipsExternalCause::select('code', 'name')->get();
            // $procedure_codes = RipsProcedureCode::select('code', 'name')->take(20)->get();
            // $cies = RipsCie::select('code', 'name')->take(20)->get();
            return response()->json([
                'success' => true,
                'data' => [
                    'document_types' => $documentTypes,
                    'care_modalities' => $care_modalities,
                    'service_groups' => $service_groups,
                    'main_diagnosis_types' => $main_diagnosis_types,
                    'collection_concepts' => $collection_concepts,
                    'service_codes' => $service_codes,
                    'consultation_purposes' => $consultation_purposes,
                    'external_causes' => $external_causes,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving tables',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProcedureCodes(Request $request) {
        try {
            $search = $request->get('search');
            if(!$search) {
                $search = '01';
            }
            $procedure_codes = RipsProcedureCode::filter($search)->take(20)->get();
            return response()->json([
                'success' => true,
                'data' => $procedure_codes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving procedure codes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCies(Request $request) {
        try {
            $search = $request->get('search');
            if(!$search) {
                $search = 'FIEBRE';
            }
            $cies = RipsCie::filter($search)->take(20)->get();
            return response()->json([
                'success' => true,
                'data' => $cies
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving cies',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
