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

        if(!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Credenciales invalidas', 'code' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }

        $user = auth()->user();
        $company = $user->company;
        if(!$company) {
            return response()->json(['error' => 'Usuario no tiene una empresa asignada', 'code' => Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }
        $userResponse = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'api_token' => $user->api_token,
            'company' => [
                "id" => $company->id,
                "identification_number" => $company->identification_number,
                "dv" => $company->dv,
                "type_environment_id" => $company->type_environment_id,
                "type_operation_id" => $company->type_operation_id,
                "type_document_identification_id" => $company->type_document_identification_id,
                "type_currency_id" => $company->type_currency_id,
                "type_organization_id" => $company->type_organization_id,
                "type_regime_id" => $company->type_regime_id,
                "type_liability_id" => $company->type_liability_id,
                "municipality_id" => $company->municipality_id,
                "merchant_registration" => $company->merchant_registration,
                "address" => $company->address,
                "phone" => $company->phone,
                "state" => $company->state,
            ]
        ];

        return response()->json(['data' => $userResponse, 'code' => Response::HTTP_OK], Response::HTTP_OK);
    }
}
