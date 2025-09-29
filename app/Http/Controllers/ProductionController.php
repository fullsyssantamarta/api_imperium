<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ProductionController extends Controller
{
    public function index($company)
    {
        $company = Company::with(['software', 'user'])->where('identification_number', $company)->first();
        // Validar primero el campo de la empresa, luego el del software
        $isProduction = false;
        if ($company) {
            if ((string)$company->type_environment_id === '1') {
                $isProduction = true;
            } elseif ($company->software && (string)$company->software->type_environment_id === '1') {
                $isProduction = true;
            }
        }
        return view('company.production', [
            'company' => $company,
            'isProduction' => $isProduction
        ]);
    }

    public function process(Request $request, $company)
    {
        if ($request->ajax()) {
            $step = $request->input('step', 1);
            $testSetId = trim($request->input('test_set_id'));
            $zipkey = $request->input('zipkey');
            // \Log::info('Paso a producción iniciado', [
            //     'step' => $step,
            //     'testSetId' => $testSetId,
            //     'zipkey' => $zipkey,
            //     'company' => $company
            // ]);
            $company = Company::with('software', 'user')->where('identification_number', $company)->first();
            if (!$company) {
                // \Log::error('Empresa no encontrada', ['company' => $company]);
                return response()->json(['error' => 'Empresa no encontrada.']);
            }
            $token = $company->user->api_token ?? null;
            if (!$token) {
                // \Log::error('Token de usuario principal no encontrado', ['company_id' => $company->id]);
                return response()->json(['error' => 'No se encontró el token del usuario principal.']);
            }
            $client = new Client(['base_uri' => rtrim(env('APP_URL'), '/') . '/api']);
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ];

            if ($step == 1) {
                // \Log::info('Paso 1: Enviar factura de prueba', ['testSetId' => $testSetId]);
                if (empty($testSetId)) {
                    // \Log::warning('TestSetId vacío');
                    return response()->json(['error' => 'Debe ingresar el TestSetId entregado por la DIAN.']);
                }
                $body = [
                    'type_document_id' => '1',
                    'prefix' => 'SETP'
                ];
                try {
                    $response = $client->post('/api/ubl2.1/next-consecutive', [
                        'headers' => $headers,
                        'json' => $body
                    ]);
                    $data = json_decode($response->getBody(), true);
                    // \Log::info('Respuesta next-consecutive', ['data' => $data]);
                    $consecutive = isset($data['number']) ? (int)$data['number'] : 990000001;
                } catch (\Exception $e) {
                    // \Log::error('Error obteniendo consecutivo', ['exception' => $e]);
                    $consecutive = 990000001;
                }
                $json = [
                    "number" => $consecutive,
                    "type_document_id" => 1,
                    "date" => now()->format('Y-m-d'),
                    "time" => now()->format('H:i:s'),
                    "resolution_number" => 18760000001,
                    "prefix" => "SETP",
                    "customer" => [
                        "identification_number" => "900428042",
                        "name" => "TAMPAC TECNOLOGÍA EN AUTOMATIZACIÓN SAS"
                    ],
                    "payment_form" => [
                        "payment_form_id" => 1,
                        "payment_method_id" => 30,
                        "payment_due_date" => now()->format('Y-m-d'),
                        "duration_measure" => "30"
                    ],
                    "legal_monetary_totals" => [
                        "line_extension_amount" => "2000.00",
                        "tax_exclusive_amount" => "2000.00",
                        "tax_inclusive_amount" => "2380.00",
                        "payable_amount" => "2380.00"
                    ],
                    "tax_totals" => [
                        [
                            "tax_id" => 1,
                            "tax_amount" => "380.00",
                            "percent" => "19",
                            "taxable_amount" => "2000.00"
                        ]
                    ],
                    "invoice_lines" => [
                        [
                            "unit_measure_id" => 70,
                            "invoiced_quantity" => "1",
                            "line_extension_amount" => "1000.00",
                            "free_of_charge_indicator" => false,
                            "description" => "Producto de prueba 1",
                            "code" => "PRUEBA1",
                            "type_item_identification_id" => 4,
                            "price_amount" => "1000.00",
                            "base_quantity" => "1",
                            "tax_totals" => [
                                [
                                    "tax_id" => 1,
                                    "tax_amount" => "190.00",
                                    "taxable_amount" => "1000.00",
                                    "percent" => "19.00"
                                ]
                            ]
                        ],
                        [
                            "unit_measure_id" => 70,
                            "invoiced_quantity" => "1",
                            "line_extension_amount" => "1000.00",
                            "free_of_charge_indicator" => false,
                            "description" => "Producto de prueba 2",
                            "code" => "PRUEBA2",
                            "type_item_identification_id" => 4,
                            "price_amount" => "1000.00",
                            "base_quantity" => "1",
                            "tax_totals" => [
                                [
                                    "tax_id" => 1,
                                    "tax_amount" => "190.00",
                                    "taxable_amount" => "1000.00",
                                    "percent" => "19.00"
                                ]
                            ]
                        ]
                    ]
                ];
                try {
                    $response = $client->post("/api/ubl2.1/invoice/{$testSetId}", [
                        'headers' => $headers,
                        'json' => $json
                    ]);
                    $result = json_decode($response->getBody(), true);
                    // \Log::info('Respuesta envío factura de prueba', ['result' => $result]);
                    $zipkey = $result['ResponseDian']['Envelope']['Body']['SendTestSetAsyncResponse']['SendTestSetAsyncResult']['ZipKey'] ?? null;

                    if (!$zipkey) {
                        $mensaje = $result['message'] ?? 'No se obtuvo ZipKey.';
                        // \Log::error('No se obtuvo ZipKey', ['result' => $result]);
                        return response()->json(['error' => $mensaje]);
                    }
                    return response()->json(['success' => true, 'zipkey' => $zipkey]);
                } catch (\Exception $e) {
                    // \Log::error('Error al enviar el documento', ['exception' => $e]);
                    return response()->json(['error' => 'Error al enviar el documento: ' . $e->getMessage()]);
                }
            }

            if ($step == 2) {
                // \Log::info('Paso 2: Consultar ZipKey', ['zipkey' => $zipkey]);
                if (!$zipkey) {
                    // \Log::warning('ZipKey vacío');
                    return response()->json(['error' => 'No se recibió ZipKey.']);
                }
                try {
                    $body = [
                        "sendmail" => false,
                        "sendmailtome" => false,
                        "is_payroll" => false,
                        "is_eqdoc" => true
                    ];
                    $response = $client->post("/api/ubl2.1/status/zip/{$zipkey}", [
                        'headers' => $headers,
                        'json' => $body
                    ]);
                    // \Log::info('Respuesta RAW de la DIAN al consultar ZipKey', [
                    //     'raw_response' => (string) $response->getBody()
                    // ]);
                    $data = json_decode($response->getBody(), true);
                    // \Log::info('Respuesta ARRAY consulta ZipKey', [
                    //     'data' => $data
                    // ]);

                    $dianResponse = $data['ResponseDian']['Envelope']['Body']['GetStatusZipResponse']['GetStatusZipResult']['DianResponse'] ?? null;
                    if ($dianResponse && isset($dianResponse['IsValid']) && $dianResponse['IsValid'] === "false") {
                        $desc = $dianResponse['StatusDescription'] ?? 'Error desconocido';
                        $statusCode = $dianResponse['StatusCode'] ?? '';
                        $statusMsg = $dianResponse['StatusMessage'] ?? '';
                        $errorMsg = '';
                        if (stripos($desc, 'proceso de validación') !== false) {
                            return response()->json([
                                'error' => 'El documento está en proceso de validación en la DIAN. Por favor, espera unos minutos y vuelve a consultar el ZipKey.'
                            ]);
                        }
                        if (is_array($statusCode)) {
                            $statusCode = json_encode($statusCode);
                        }
                        if (is_array($statusMsg)) {
                            $statusMsg = json_encode($statusMsg);
                        }

                        if (isset($dianResponse['ErrorMessage']['string'])) {
                            if (is_array($dianResponse['ErrorMessage']['string'])) {
                                $errorMsg = implode('<br>', $dianResponse['ErrorMessage']['string']);
                            } else {
                                $errorMsg = $dianResponse['ErrorMessage']['string'];
                            }
                        }

                        $fullMsg = $desc;
                        if ($statusMsg) {
                            $fullMsg .= '<br>' . $statusMsg;
                        }
                        if ($errorMsg) {
                            $fullMsg .= '<br>' . $errorMsg;
                        }

                        return response()->json(['error' => $fullMsg]);
                    }
                    return response()->json(['success' => true, 'zipkey_status' => $data]);
                } catch (\Exception $e) {
                    // \Log::error('Error consultando ZipKey', ['exception' => $e]);
                    return response()->json(['error' => 'Error consultando ZipKey: ' . $e->getMessage()]);
                }
            }

            if ($step == 3) {
                // \Log::info('Paso 3: Cambiar ambiente');
                try {
                    $envData = [
                        "type_environment_id" => 1,
                        "payroll_type_environment_id" => 2,
                        "eqdocs_type_environment_id" => 2
                    ];
                    $envResponse = $client->put('/api/ubl2.1/config/environment', [
                        'headers' => $headers,
                        'json' => $envData
                    ]);
                    $envResult = json_decode($envResponse->getBody(), true);
                    // \Log::info('Respuesta cambio de ambiente', ['envResult' => $envResult]);
                    return response()->json(['success' => true, 'env_result' => $envResult]);
                } catch (\Exception $e) {
                    // \Log::error('Error cambiando ambiente', ['exception' => $e]);
                    return response()->json(['error' => 'Error cambiando ambiente: ' . $e->getMessage()]);
                }
            }
        }

        return back()->with('error', 'Petición inválida.');
    }
    private function consultarZipKey($zipKey, $token)
    {
        $baseUrl = rtrim(env('APP_URL'), '/') . '/api';
        $client = new \GuzzleHttp\Client(['base_uri' => $baseUrl]);
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
        $body = [
            "sendmail" => false,
            "sendmailtome" => false,
            "is_payroll" => false,
            "is_eqdoc" => true
        ];

        try {
            $response = $client->post("/api/ubl2.1/status/zip/{$zipKey}", [
                'headers' => $headers,
                'json' => $body
            ]);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            // \Log::error('Error consultando ZipKey', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    public function consultarResoluciones(Request $request, $company)
    {
        $company = Company::with(['software', 'user'])->where('identification_number', $company)->first();
        if (!$company || !$company->software) {
            return response()->json(['error' => 'Empresa o software no encontrado'], 404);
        }

        $token = $company->user->api_token ?? null;
        $IDSoftware = $company->software->identifier ?? null;

        if (!$token || !$IDSoftware) {
            return response()->json(['error' => 'Token o IDSoftware no disponible'], 400);
        }

        try {
            $client = new \GuzzleHttp\Client(['base_uri' => rtrim(env('APP_URL'), '/') . '/api']);
            $response = $client->post('/api/ubl2.1/numbering-range', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'IDSoftware' => $IDSoftware
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
