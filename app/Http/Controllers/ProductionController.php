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
    \Log::info('Iniciando proceso de paso a producción', ['company' => $company]);

    // 1. Buscar la empresa
    $company = Company::with('software')->where('identification_number', $company)->first();
    if (!$company) {
        \Log::error('Empresa no encontrada', ['company' => $company]);
        return back()->with('error', 'Empresa no encontrada.');
    }
    \Log::info('Empresa encontrada', ['company_id' => $company->id]);

    // 2. Verificar si ya está en producción (empresa o software)
    \Log::info('Valores de ambiente antes de validar', [
        'company_type_environment_id' => $company->type_environment_id,
        'software_type_environment_id' => $company->software ? $company->software->type_environment_id : null,
        'company_type_environment_id_tipo' => gettype($company->type_environment_id),
        'software_type_environment_id_tipo' => $company->software ? gettype($company->software->type_environment_id) : null,
    ]);
    if (
        ((string)$company->type_environment_id === '1') ||
        ($company->software && (string)$company->software->type_environment_id === '1')
    ) {
        \Log::warning('La empresa o el software ya están en ambiente de producción', [
            'company_id' => $company->id,
            'company_type_environment_id' => $company->type_environment_id,
            'software_type_environment_id' => $company->software ? $company->software->type_environment_id : null
        ]);
        return back()->with('error', 'La empresa o el software ya están en ambiente de producción.');
    }

    // 3. Obtener el usuario principal y su token
    $user = $company->user;
    if (!$user || !$user->api_token) {
        \Log::error('No se encontró el token del usuario principal', ['company_id' => $company->id, 'user' => $user]);
        return back()->with('error', 'No se encontró el token del usuario principal.');
    }
    $token = $user->api_token;
    \Log::info('Token del usuario principal obtenido', ['user_id' => $user->id]);

    // 4. Consultar el consecutivo actual
    $baseUrl = rtrim(env('APP_URL'), '/') . '/api';
    \Log::info('API base_url usada', ['base_url' => $baseUrl]);
    $client = new Client(['base_uri' => $baseUrl]);
    $headers = [
        'Authorization' => 'Bearer ' . $token,
        'Accept'        => 'application/json',
    ];
    $body = [
        'type_document_id' => '1',
        'prefix' => 'SETP'
    ];

    try {
        \Log::info('Consultando next-consecutive', ['body' => $body]);
        $response = $client->post('/api/ubl2.1/next-consecutive', [
            'headers' => $headers,
            'json' => $body
        ]);
        $data = json_decode($response->getBody(), true);
        $consecutive = isset($data['number']) ? (int)$data['number'] : 994999999;
        $fromNextConsecutive = isset($data['number']);
        \Log::info('Consecutivo obtenido', ['consecutive' => $consecutive, 'from_next_consecutive' => $fromNextConsecutive]);
    } catch (\Exception $e) {
        \Log::error('Error consultando next-consecutive', ['error' => $e->getMessage()]);
        $consecutive = 994999999;
        $fromNextConsecutive = false;
    }

    // 5. Preparar el JSON de prueba
    $testSetId = trim($request->input('test_set_id'));
    if (empty($testSetId)) {
        \Log::error('TestSetId vacío');
        return back()->with('error', 'Debe ingresar el TestSetId entregado por la DIAN.');
    }
    \Log::info('TestSetId recibido', ['testSetId' => $testSetId]);

    $json = [
        "number" => $consecutive,
        "type_document_id" => 1,
        "date" => now()->format('Y-m-d'),
        "time" => now()->format('H:i:s'),
        "resolution_number" => "18760000001",
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

    // 6. Intentar enviar el documento, si ya existe, restar uno al consecutivo y reintentar
    $maxTries = 10;
$tries = 0;
do {
    try {
        \Log::info('Enviando documento a endpoint de pruebas', [
            'endpoint' => "/api/ubl2.1/invoice/{$testSetId}",
            'json' => $json,
            'try' => $tries + 1
        ]);
        $response = $client->post("/api/ubl2.1/invoice/{$testSetId}", [
            'headers' => $headers,
            'json' => $json
        ]);
        $result = json_decode($response->getBody(), true);

        \Log::info('Respuesta del endpoint', ['result' => $result]);

        if (isset($result['message']) && str_contains($result['message'], 'ya se generó el documento anteriormente')) {
            if ($fromNextConsecutive) {
                $json['number']++;
                \Log::warning('Documento ya generado anteriormente, sumando consecutivo', [
                    'nuevo_consecutivo' => $json['number'],
                    'from_next_consecutive' => $fromNextConsecutive
                ]);
            } else {
                $json['number']--;
                \Log::warning('Documento ya generado anteriormente, restando consecutivo', [
                    'nuevo_consecutivo' => $json['number'],
                    'from_next_consecutive' => $fromNextConsecutive
                ]);
            }
            $tries++;
            continue;
        }

        // Consultar ZipKey si existe
        $zipKey = null;
        if (isset($result['ResponseDian']['Envelope']['Body']['SendTestSetAsyncResponse']['SendTestSetAsyncResult']['ZipKey'])) {
            $zipKey = $result['ResponseDian']['Envelope']['Body']['SendTestSetAsyncResponse']['SendTestSetAsyncResult']['ZipKey'];
            \Log::info('ZipKey obtenido', ['zipkey' => $zipKey]);
        }

        $zipKeyStatus = null;
        if ($zipKey) {
            $zipKeyStatus = $this->consultarZipKey($zipKey, $token);
            \Log::info('Estado ZipKey consultado', ['zipkey_status' => $zipKeyStatus]);

            // Revisar si la respuesta contiene el rechazo por documento procesado anteriormente
            if (
                isset($zipKeyStatus['ResponseDian']['Envelope']['Body']['GetStatusZipResponse']['GetStatusZipResult']['DianResponse']['ErrorMessage']['string'])
                && is_array($zipKeyStatus['ResponseDian']['Envelope']['Body']['GetStatusZipResponse']['GetStatusZipResult']['DianResponse']['ErrorMessage']['string'])
            ) {
                $errores = $zipKeyStatus['ResponseDian']['Envelope']['Body']['GetStatusZipResponse']['GetStatusZipResult']['DianResponse']['ErrorMessage']['string'];
                foreach ($errores as $error) {
                    if (str_contains($error, 'Regla: 90, Rechazo: Documento procesado anteriormente')) {
                        // Ajustar consecutivo y reintentar
                        if ($fromNextConsecutive) {
                            $json['number']++;
                        } else {
                            $json['number']--;
                        }
                        \Log::warning('ZipKey rechazado por documento procesado anteriormente, ajustando consecutivo y reintentando', [
                            'nuevo_consecutivo' => $json['number'],
                            'try' => $tries + 1
                        ]);
                        $tries++;
                        continue 2; // Salta al siguiente ciclo del do-while
                    }
                }
            }
        }

        // Si no hay rechazo, salir del ciclo
        break;

    } catch (\Exception $e) {
        \Log::error('Error al enviar el documento', [
            'error' => $e->getMessage(),
            'try' => $tries + 1,
            'json' => $json
        ]);
        return back()->with('error', 'Error al enviar el documento: ' . $e->getMessage());
    }
} while ($tries < $maxTries);

if ($tries >= $maxTries) {
    \Log::error('No se pudo enviar el documento después de varios intentos');
    return back()->with('error', 'No se pudo enviar el documento, intente manualmente.');
}
try {
    $envData = [
        "type_environment_id" => 1,
        "payroll_type_environment_id" => 2,
        "eqdocs_type_environment_id" => 2
    ];
    \Log::info('Cambiando ambiente a producción', ['envData' => $envData]);
    $envResponse = $client->put('/api/ubl2.1/config/environment', [
        'headers' => $headers,
        'json' => $envData
    ]);
    $envResult = json_decode($envResponse->getBody(), true);
    \Log::info('Respuesta cambio de ambiente', ['envResult' => $envResult]);
} catch (\Exception $e) {
    \Log::error('Error cambiando ambiente a producción', ['error' => $e->getMessage()]);
    return back()->with('error', 'Documento enviado, pero no se pudo cambiar el ambiente a producción: ' . $e->getMessage());
}

return back()->with([
    'success' => 'Documento enviado correctamente para paso a producción y ambiente cambiado a producción.',
    'zipkey' => $zipKey ?? null,
    'zipkey_status' => $zipKeyStatus ?? null,
    'env_result' => $envResult ?? null
]);
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
        \Log::error('Error consultando ZipKey', ['error' => $e->getMessage()]);
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
