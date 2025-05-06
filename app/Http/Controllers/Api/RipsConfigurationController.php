<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\RipsConfiguration;

class RipsConfigurationController extends Controller
{
    public function setConfiguration(Request $request)
    {
        $this->validate($request, [
            'type_document_identification_id' => 'required|integer|exists:type_document_identifications,id',
            'number_identification' => 'required|string',
            'password' => 'required|string',
            'url' => 'required|url',
        ]);

        $configuration = $request->all();

        $user = auth()->user();
        $company = $this->getCompanyId();

        $config = RipsConfiguration::updateOrCreate(
            ['company_id' => $company->id],
            [
                'type_document_identification_id' => $configuration['type_document_identification_id'],
                'number_identification' => $configuration['number_identification'],
                'password' => $configuration['password'],
                'url' => $configuration['url'],
            ]
        );
        return response()->json([
            'success' => true,
            'message' => 'Configuraón actualizada/creada exitosamente',
            'configuration' => $config,
        ], 200);
    }
}
