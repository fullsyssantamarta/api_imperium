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
        $users = $company->users()->paginate(15);

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
            'url_fevrips' => 'nullable|url',
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
                'url_fevrips' => $request->url_fevrips,
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
            'url_fevrips' => 'nullable|url',
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
                'url_fevrips' => $request->url_fevrips,
            ]);

            return redirect()->back()->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el usuario.');
        }
    }

    public function emailIndex($companyId)
    {
        $company = Company::with('user')->findOrFail($companyId);
        $user = $company->user;

        // Obtener configuración actual
        $emailConfig = [
            'mail_host' => $user->mail_host ?: config('mail.host'),
            'mail_port' => $user->mail_port ?: config('mail.port'),
            'mail_username' => $user->mail_username ?: config('mail.username'),
            'mail_password' => $user->mail_password ? '********' : '',
            'mail_encryption' => $user->mail_encryption ?: config('mail.encryption'),
            'has_custom_config' => !empty($user->mail_host)
        ];

        return view('company.email', compact('company', 'emailConfig'));
    }

    public function emailStore(Request $request, $companyId)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|string|in:tls,ssl',
        ]);

        try {
            $company = Company::findOrFail($companyId);
            $user = $company->user;

            $user->update([
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_username' => $request->mail_username,
                'mail_password' => $request->mail_password,
                'mail_encryption' => $request->mail_encryption,
            ]);

            return redirect()->back()->with('success', 'Configuración de correo actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar la configuración de correo.');
        }
    }
}
