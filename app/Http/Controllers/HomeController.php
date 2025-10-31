<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Company;
use App\Document;
use App\Resolution;
use App\ReceivedDocument;
use App\DocumentPayroll;
use App\TypeRegime;
use App\TypeLiability;
use App\Municipality;
use App\TypeDocumentIdentification;
use Illuminate\Validation\ValidationException;
use Exception;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $companies = Company::get()->transform( function($row) {
            $documents = Document::where('identification_number', $row->identification_number)->count();
            $row->total_documents = $documents;
            return $row;
        });

        // Datos para los selects del modal de edición
        $type_regimes = TypeRegime::all();
        $type_liabilities = TypeLiability::all();
        $municipalities = Municipality::all();
        $type_document_identifications = TypeDocumentIdentification::all();

        return view('home', compact('companies', 'type_regimes', 'type_liabilities', 'municipalities', 'type_document_identifications'));
    }

    public function tools()
    {
        return view('tools');
    }

    public function company(Company $company)
    {
        $documents = Document::where('identification_number', $company->identification_number)->orderBy('id', 'DESC')->paginate(20);

        $resolution_credit_notes = Resolution::where('type_document_id', 4)->where('company_id', $company->id)->get();

        $token_company = $company->user->api_token;

        return view('company.documents', ['company' => $company, 'documents' => $documents, 'resolution_credit_notes' => $resolution_credit_notes, 'token_company' => $token_company]);
    }

    public function getXml(Company $company, $cufe)
    {
        $token = $company->user->api_token;
        $url = url('/api/ubl2.1/xml/document/'.$cufe);

        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ]
        ]);

        // dd($response);
        $responseBody = json_decode($response->getBody(), true);

        // Manejar la respuesta
        if ($response->getStatusCode() == 200) {
            return response()->json($responseBody);
        } else {
            return response()->json([
                'error' => 'Error al hacer la solicitud a la API',
                'status_code' => $response->getStatusCode(),
                'body' => $responseBody,
            ], $response->getStatusCode());
        }
    }

    // replica de SellerLoginController@SellersRadianEventsView
    public function events($company_idnumber){
        $documents = ReceivedDocument::where('customer','=',$company_idnumber)->where('state_document_id', '=', 1)->paginate(10);
        return view('company.events', compact('documents', 'company_idnumber'));
    }

    // replica de SellerLoginController@SellersPayrolls
    public function payrolls($company_idnumber)
    {
        $documents = DocumentPayroll::where('state_document_id', '=', 1)->where('identification_number', $company_idnumber)->paginate(20);
        return view('company.payrolls', compact('documents', 'company_idnumber'));
    }

    public function update(Request $request, $companyId)
    {
        try {
            $request->validate([
                'identification_number' => 'required|numeric|digits_between:1,15|unique:companies,identification_number,' . $companyId,
                'dv' => 'required|numeric|digits:1',
                'type_regime_id' => 'required|exists:type_regimes,id',
                'type_liability_id' => 'required|exists:type_liabilities,id',
                'municipality_id' => 'required|exists:municipalities,id',
                'merchant_registration' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'type_document_identification_id' => 'required|exists:type_document_identifications,id',
            ], [
                'identification_number.required' => 'El número de identificación es obligatorio.',
                'identification_number.numeric' => 'El número de identificación debe ser numérico.',
                'identification_number.digits_between' => 'El número de identificación debe tener entre 1 y 15 dígitos.',
                'identification_number.unique' => 'Ya existe una empresa con este número de identificación.',
                'dv.required' => 'El dígito de verificación es obligatorio.',
                'dv.numeric' => 'El dígito de verificación debe ser numérico.',
                'dv.digits' => 'El dígito de verificación debe ser de 1 dígito.',
                'type_regime_id.required' => 'El tipo de régimen es obligatorio.',
                'type_regime_id.exists' => 'El tipo de régimen seleccionado no es válido.',
                'type_liability_id.required' => 'El tipo de responsabilidad es obligatorio.',
                'type_liability_id.exists' => 'El tipo de responsabilidad seleccionado no es válido.',
                'municipality_id.required' => 'El municipio es obligatorio.',
                'municipality_id.exists' => 'El municipio seleccionado no es válido.',
                'merchant_registration.required' => 'La matrícula mercantil es obligatoria.',
                'merchant_registration.string' => 'La matrícula mercantil debe ser texto.',
                'merchant_registration.max' => 'La matrícula mercantil no puede tener más de 255 caracteres.',
                'address.required' => 'La dirección es obligatoria.',
                'address.string' => 'La dirección debe ser texto.',
                'address.max' => 'La dirección no puede tener más de 255 caracteres.',
                'phone.required' => 'El teléfono es obligatorio.',
                'phone.string' => 'El teléfono debe ser texto.',
                'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
                'type_document_identification_id.required' => 'El tipo de documento es obligatorio.',
                'type_document_identification_id.exists' => 'El tipo de documento seleccionado no es válido.',
            ]);

            $company = Company::findOrFail($companyId);

            $company->update([
                'identification_number' => $request->identification_number,
                'dv' => $request->dv,
                'type_regime_id' => $request->type_regime_id,
                'type_liability_id' => $request->type_liability_id,
                'municipality_id' => $request->municipality_id,
                'merchant_registration' => $request->merchant_registration,
                'address' => $request->address,
                'phone' => $request->phone,
                'type_document_identification_id' => $request->type_document_identification_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Empresa actualizada exitosamente.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la empresa: ' . $e->getMessage()
            ], 500);
        }
    }
}
