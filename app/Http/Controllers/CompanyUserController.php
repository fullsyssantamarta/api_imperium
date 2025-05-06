<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Company;
use App\User;
use App\HealthTypeDocumentIdentification;

class CompanyUserController extends Controller
{
    public function index($companyId)
    {
        $company = Company::with('users')->findOrFail($companyId);
        $document_types = HealthTypeDocumentIdentification::all();
        $users = $company->users;

        return view('company.users', compact('company', 'users', 'document_types'));
    }

    public function store(Request $request, $companyId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'can_rips' => 'nullable|boolean',
            'can_health' => 'nullable|boolean',
        ]);

        try {
            $company = Company::findOrFail($companyId);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'can_rips' => $request->has('can_rips'),
                'can_health' => $request->has('can_health'),
                'api_token' => hash('sha256',  Str::random(80)),
                'code_service_provider' => $request->code_service_provider,'document_type_id' => $request->document_type_id,
                'document_number' => $request->document_number,
            ]);

            $company->users()->attach($user->id);

            return redirect()->back()->with('success', 'Usuario creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al crear el usuario.');
        }
    }

    public function update(Request $request, $companyId, $userId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6|confirmed',
            'can_rips' => 'nullable|boolean',
            'can_health' => 'nullable|boolean',
        ]);

        try {
            $user = User::findOrFail($userId);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'can_rips' => $request->has('can_rips'),
                'can_health' => $request->has('can_health'),
                'code_service_provider' => $request->code_service_provider,
                'document_type_id' => $request->document_type_id,
                'document_number' => $request->document_number,
            ]);

            return redirect()->back()->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el usuario.');
        }
    }
}
