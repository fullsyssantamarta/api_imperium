<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Credenciales inválidas', 'code' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }

        $user = auth()->user();
        $company = $user->company; // Relación directa
        $companies = $user->companies; // Relación many-to-many

        // Validar que el usuario tenga al menos una relación con una compañía
        if (!$company && $companies->isEmpty()) {
            return response()->json(['error' => 'Usuario no tiene una empresa asignada', 'code' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }

        // Determinar la compañía activa
        if ($company) {
            // Caso 1: Usuario con relación directa a `company`
            $activeCompany = $company;
        } else {
            // Caso 2: Usuario con relación many-to-many a `companies`
            $activeCompany = $companies->first();

            // Validar permisos (can_rips o can_health)
            if (!$user->can_rips && !$user->can_health) {
                return response()->json(['error' => 'Usuario no tiene permisos para acceder a esta aplicación', 'code' => Response::HTTP_FORBIDDEN], Response::HTTP_FORBIDDEN);
            }
        }



        // Construir la respuesta del usuario
        $userResponse = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'api_token' => $user->api_token,
            'can_rips' => $user->can_rips,
            'can_health' => $user->can_health,
            'code_service_provider' => $user->code_service_provider,
            'is_parent_user' => !$companies->isEmpty(),
            'company' => [
                "id" => $activeCompany->id,
                "identification_number" => $activeCompany->identification_number,
                "dv" => $activeCompany->dv,
                "type_environment_id" => $activeCompany->type_environment_id,
                "type_operation_id" => $activeCompany->type_operation_id,
                "type_document_identification_id" => $activeCompany->type_document_identification_id,
                "type_currency_id" => $activeCompany->type_currency_id,
                "type_organization_id" => $activeCompany->type_organization_id,
                "type_regime_id" => $activeCompany->type_regime_id,
                "type_liability_id" => $activeCompany->type_liability_id,
                "municipality_id" => $activeCompany->municipality_id,
                "merchant_registration" => $activeCompany->merchant_registration,
                "address" => $activeCompany->address,
                "phone" => $activeCompany->phone,
                "state" => $activeCompany->state,
            ]
        ];

        return response()->json(['data' => $userResponse, 'code' => Response::HTTP_OK], Response::HTTP_OK);
    }
}
