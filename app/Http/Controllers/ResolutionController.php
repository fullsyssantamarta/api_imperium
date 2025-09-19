<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resolution;
use App\Company;
use App\User;
use DB;
use Exception;

class ResolutionController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::where('identification_number',$request->company)->first();
        $resolutions = Resolution::where('company_id', $company->id)
            ->with(['type_environment', 'type_document'])
            ->paginate(10);

        return view('company.resolutions', compact(['resolutions', 'company']));
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
