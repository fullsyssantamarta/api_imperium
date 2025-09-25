<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resolution;
use App\Company;
use App\User;
use App\TypeDocument;
use DB;
use Exception;
use Illuminate\Validation\ValidationException;

class ResolutionController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::where('identification_number',$request->company)->first();
        $resolutions = Resolution::where('company_id', $company->id)
            ->with(['type_environment', 'type_document'])
            ->paginate(10);

        // Obtener tipos de documento para el modal
        $typeDocuments = TypeDocument::whereIn('code', ['01', '02', '03', '91', '92', '93', '94', '1', '2', '05', '95'])->get();

        return view('company.resolutions', compact(['resolutions', 'company', 'typeDocuments']));
    }

    /**
     * Store a new resolution
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $companyNumber)
    {
        try {
            $typeDocument = TypeDocument::find($request->type_document_id);

            $simpleCodes = ['91', '92', '93', '94'];
            $isSimpleType = $typeDocument && in_array($typeDocument->code, $simpleCodes);

            $rules = [
                'resolution' => 'required|string|max:255',
                'type_document_id' => 'required|exists:type_documents,id',
                'prefix' => 'required|string|max:4',
                'from' => 'required|integer|min:1',
                'to' => 'required|integer|min:1|gte:from',
            ];

            $messages = [
                'resolution.required' => 'El número de resolución es obligatorio.',
                'resolution_date.required' => 'La fecha de resolución es obligatoria.',
                'resolution_date.date' => 'La fecha de resolución debe ser una fecha válida.',
                'type_document_id.required' => 'El tipo de documento es obligatorio.',
                'type_document_id.exists' => 'El tipo de documento seleccionado no es válido.',
                'prefix.required' => 'El prefijo es obligatorio.',
                'prefix.string' => 'El prefijo debe ser una cadena de texto.',
                'prefix.max' => 'El prefijo no puede tener más de 4 caracteres.',
                'from.required' => 'El rango inicial es obligatorio.',
                'from.integer' => 'El rango inicial debe ser un número entero.',
                'from.min' => 'El rango inicial debe ser mayor a 0.',
                'to.required' => 'El rango final es obligatorio.',
                'to.integer' => 'El rango final debe ser un número entero.',
                'to.gte' => 'El rango final debe ser mayor o igual al rango inicial.',
            ];

            if (!$isSimpleType) {
                $rules = array_merge($rules, [
                    'resolution_date' => 'required|date',
                    'technical_key' => 'required|string|max:255',
                    'date_from' => 'required|date',
                    'date_to' => 'required|date|after_or_equal:date_from',
                ]);

                $messages = array_merge($messages, [
                    'technical_key.required' => 'La clave técnica es obligatoria.',
                    'date_from.required' => 'La fecha de inicio de vigencia es obligatoria.',
                    'date_from.date' => 'La fecha de inicio de vigencia debe ser una fecha válida.',
                    'date_to.required' => 'La fecha de fin de vigencia es obligatoria.',
                    'date_to.date' => 'La fecha de fin de vigencia debe ser una fecha válida.',
                    'date_to.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                ]);
            }

            $request->validate($rules, $messages);

            $company = Company::where('identification_number', $companyNumber)->first();

            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compañía no encontrada.'
                ], 404);
            }

            DB::beginTransaction();

            $resolutionData = [
                'company_id' => $company->id,
                'type_document_id' => $request->type_document_id,
                'prefix' => $request->prefix,
                'from' => $request->from,
                'to' => $request->to,
                'type_environment_id' => $company->type_environment_id,
            ];

            if (!$isSimpleType) {
                $resolutionData = array_merge($resolutionData, [
                    'resolution' => $request->resolution,
                    'resolution_date' => $request->resolution_date,
                    'technical_key' => $request->technical_key,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                ]);
            }

            $resolution = Resolution::create($resolutionData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Resolución creada exitosamente.',
                'resolution' => $resolution->load('type_document')
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la resolución: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a resolution
     *
     * @param Request $request
     * @param int $resolutionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $companyNumber, $resolutionId)
    {
        try {
            $resolution = Resolution::findOrFail($resolutionId);
            $company = Company::where('identification_number', $companyNumber)->first();

            if (!$company || $resolution->company_id !== $company->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resolución no encontrada o no pertenece a la compañía.'
                ], 404);
            }

            $typeDocument = TypeDocument::find($request->type_document_id);
            $simpleCodes = ['91', '92', '93', '94'];
            $isSimpleType = $typeDocument && in_array($typeDocument->code, $simpleCodes);

            $rules = [
                'type_document_id' => 'required|exists:type_documents,id',
                'from' => 'required|integer|min:1',
                'to' => 'required|integer|min:1|gte:from',
            ];

            $messages = [
                'type_document_id.required' => 'El tipo de documento es obligatorio.',
                'type_document_id.exists' => 'El tipo de documento seleccionado no es válido.',
                'from.required' => 'El rango inicial es obligatorio.',
                'from.integer' => 'El rango inicial debe ser un número entero.',
                'from.min' => 'El rango inicial debe ser mayor a 0.',
                'to.required' => 'El rango final es obligatorio.',
                'to.integer' => 'El rango final debe ser un número entero.',
                'to.gte' => 'El rango final debe ser mayor o igual al rango inicial.',
            ];

            if (!$isSimpleType) {
                $rules = array_merge($rules, [
                    'resolution' => 'required|string|max:255',
                    'resolution_date' => 'required|date',
                    'technical_key' => 'required|string|max:255',
                    'date_from' => 'required|date',
                    'date_to' => 'required|date|after_or_equal:date_from',
                ]);

                $messages = array_merge($messages, [
                    'resolution.required' => 'El número de resolución es obligatorio.',
                    'resolution_date.required' => 'La fecha de resolución es obligatoria.',
                    'resolution_date.date' => 'La fecha de resolución debe ser una fecha válida.',
                    'technical_key.required' => 'La clave técnica es obligatoria.',
                    'date_from.required' => 'La fecha de inicio de vigencia es obligatoria.',
                    'date_from.date' => 'La fecha de inicio de vigencia debe ser una fecha válida.',
                    'date_to.required' => 'La fecha de fin de vigencia es obligatoria.',
                    'date_to.date' => 'La fecha de fin de vigencia debe ser una fecha válida.',
                    'date_to.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                ]);
            }

            $request->validate($rules, $messages);

            DB::beginTransaction();

            $updateData = [
                'type_document_id' => $request->type_document_id,
                'from' => $request->from,
                'to' => $request->to,
            ];

            if (!$isSimpleType) {
                $updateData = array_merge($updateData, [
                    'resolution' => $request->resolution,
                    'resolution_date' => $request->resolution_date,
                    'technical_key' => $request->technical_key,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                ]);
            }

            $resolution->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Resolución actualizada exitosamente.',
                'resolution' => $resolution->load('type_document')
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la resolución: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update resolution environment
     *
     * @param Request $request
     * @param int $resolutionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEnvironment(Request $request)
    {
        DB::beginTransaction();

        try {
            $resolution = Resolution::findOrFail($request->resolutionId);

            // Verificar que la resolución pertenezca a la compañía del usuario autenticado
            $company = Company::where('identification_number', $request->company)->first();

            // Actualizar el tipo de entorno de la resolución con el de la compañía
            $resolution->update([
                'type_environment_id' => $company->type_environment_id,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Entorno de la resolución actualizado con éxito');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error al actualizar la resolución: ' . $e->getMessage());
        }
    }
}
