<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\RipsController;
use App\RipsDocument;
use GuzzleHttp\Client;

class RipsDocumentController extends Controller
{
    private $ripsController;

    public function __construct(RipsController $ripsController)
    {
        $this->ripsController = $ripsController;
    }

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

    public function generateRips(Request $request, $id)
    {
        try {
            $company =$this->getCompanyId();
            $document = RipsDocument::find($id);
            $payload = $this->setPayload($document);

            // se envia el rips
            $response = $this->ripsController->processRipsWithoutInvoice($payload, $company);

            $document->update([
                'request_api' => $payload,
                'response_api' => $response->getData(),
            ]);

            if ($response->isSuccessful()) {
                $data = $response->getData();
                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'message' => 'RIPS generado correctamente'
                ], 200);
            } else {
                $error = $response->getData();
                return response()->json([
                    'success' => false,
                    'error' => $error,
                    'message' => 'Error generando RIPS'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Error generando RIPS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getServiceName($serviceTypeId) {
        $services = [
            1 => "consultas",
            2 => "procedimientos",
            3 => "urgencias",
            4 => "hospitalizacion",
            5 => "recienNacidos",
            6 => "medicamentos",
            7 => "otrosServicios"
        ];
        return $services[$serviceTypeId];
    }

    private function setPayload($document) {
        $company =$this->getCompanyId();
        $appointment = $document->appointment;
        $patient = $appointment->patient;
        $services = $document->services;
        $service_provider = $appointment->user;

        $services_payload = [];

        foreach ($services as $service) {
            $serviceTypeId = $service['service_type_id'];
            $serviceName = $this->getServiceName($serviceTypeId);
            $services_payload[$serviceName][] = [
                'codPrestador' => $service_provider->code_service_provider,
                'fechaInicioAtencion' => str_replace('T', ' ', $service['fechaInicioAtencion']),
                'numAutorizacion' => $service['numAutorizacion'] ?? null,
                'codConsulta' => $service['codConsulta'],
                'modalidadGrupoServicioTecSal' => $service['modalidadGrupoServicioTecSal'],
                'grupoServicios' => $service['grupoServicios'],
                'codServicio' => intval($service['codServicio']),
                'finalidadTecnologiaSalud' => $service['finalidadTecnologiaSalud'],
                'causaMotivoAtencion' => $service['causaMotivoAtencion'],
                'codDiagnosticoPrincipal' => $service['codDiagnosticoPrincipal'],
                'codDiagnosticoRelacionado1' => $service['codDiagnosticoRelacionado1'] ?? null,
                'codDiagnosticoRelacionado2' => $service['codDiagnosticoRelacionado2'] ?? null,
                'codDiagnosticoRelacionado3' => $service['codDiagnosticoRelacionado3'] ?? null,
                'tipoDiagnosticoPrincipal' => $service['tipoDiagnosticoPrincipal'],
                'tipoDocumentoIdentificacion' => trim($service_provider->document_type->code),
                'numDocumentoIdentificacion' => $service_provider->document_number,
                'vrServicio' => $service['vrServicio'],
                'conceptoRecaudo' => $service['conceptoRecaudo'] ?? 0,
                'valorPagoModerador' => $service['valorPagoModerador'] ?? 0,
                'numFEVPagoModerador' => $service['numFEVPagoModerador'] ?? null,
                'consecutivo' => $service['consecutivo']
            ];
        }

        $payload = [
            'rips'=> [
            'numDocumentoIdObligado' => $company->identification_number,
                'numFactura' => $document->invoice_number,
                'tipoNota' => $document->note_type,
                'numNota' => $document->note_number,
                'usuarios' => [
                    [
                        'tipoDocumentoIdentificacion' => trim($patient->typeDocumentIdentification->code_rips),
                        'numDocumentoIdentificacion' => $patient->document_number,
                        'tipoUsuario' => $patient->ripsUserType->code,
                        'fechaNacimiento' => $patient->birth_date,
                        'codSexo' => $patient->ripsGender->code,
                        'codPaisResidencia' => $patient->country_code,
                        'codMunicipioResidencia' => $patient->municipality->code,
                        'codZonaTerritorialResidencia' => $patient->ripsZone->code,
                        'incapacidad' => $patient->incapacity,
                        'codPaisOrigen' => $patient->country_code,
                        'consecutivo' => 1,
                        'servicios' => $services_payload
                    ]
                ]
            ],
            'xmlFevFile' => null
        ];

        return $payload;
    }
}
