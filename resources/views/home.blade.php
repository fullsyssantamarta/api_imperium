@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>Listado de Empresas</h2>
    <div class="right-wrapper text-end">
        <a href="{{ route('configuration_admin') }}" class="btn btn-primary btn-sm text-white mt-2 mr-2">Nueva empresa</a>
    </div>
</header>

<div class="card border">
    <div class="table-responsive">
        <table class="table table-striped table-hover" style="min-height: 150px">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Documentos generados</th>
                    <th>Nombre de usuario</th>
                    <th>Correo de usuario</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($companies as $row)
                    <tr class="table-light">
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->identification_number }}-{{ $row->dv }}</td>
                        <td>{{ $row->total_documents }}</td>
                        <td>{{ $row->user->name }}</td>
                        <td>{{ $row->user->email }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary text-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    Acciones
                                </button>
                                <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item text-secondary" href="{{ route('company', $row->identification_number)}}">Ver documentos</a>
                                    <a class="dropdown-item text-secondary" href="{{ route('company.resolutions.index', $row->identification_number)}}">Resoluciones</a>
                                    <a class="dropdown-item text-secondary" href="{{ route('company.production.index', $row->identification_number)}}">Pasar a Producción</a>
                                    <a class="dropdown-item text-secondary" href="{{ route('company.users.index', $row->id)}}">Usuarios</a>
                                    <a class="dropdown-item text-secondary" href="{{ route('company.email.index', $row->id)}}">Configurar Correo</a>
                                    <a class="dropdown-item text-secondary" href="#" data-toggle="modal" data-target="#accessModal">Acceso a la App</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-center">
                        <span class="text-muted">Cantidad de empresas registradas: {{ $companies->count() }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Modal de Acceso a la App -->
<div class="modal fade" id="accessModal" tabindex="-1" role="dialog" aria-labelledby="accessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessModalLabel">
                    Accede a la App
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-info-circle text-info" style="font-size: 3rem;"></i>
                </div>

                <h4 class="mb-4 text-dark">Formas de Acceso Disponibles</h4>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <i class="fas fa-user-tie text-primary mb-3" style="font-size: 2rem;"></i>
                                <h5 class="card-title text-primary">Usuario de Facturación</h5>
                                <p class="card-text text-muted">
                                    Utiliza el usuario principal de la empresa o un usuario de Facturaciónpara acceder a la aplicación web y generar documentos electrónicos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <i class="fas fa-users text-success mb-3" style="font-size: 2rem;"></i>
                                <h5 class="card-title text-success">Usuarios RIPS</h5>
                                <p class="card-text text-muted">
                                    Ingresa con usuarios adicionales configurados para gestión de RIPS.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="https://facturalatam.com/app" target="_blank" class="btn btn-lg px-5 py-3 text-white" style="background: linear-gradient(45deg, #007bff, #0056b3); border: none; border-radius: 50px; box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3); transition: all 0.3s ease;">
                        <i class="fas fa-rocket mr-3" style="font-size: 1.2rem;"></i>
                        <span style="font-size: 1.1rem; font-weight: bold;">INGRESAR A LA APP</span>
                        <i class="fas fa-external-link-alt ml-3"></i>
                    </a>
                </div>

                 <div class="mt-4">
                    <a href="https://facturalatam.com/apk/apidian.apk" class="btn btn-lg px-5 py-3 text-white" style="background: linear-gradient(45deg, #3DDC84, #1E7F3A); border: none; border-radius: 50px; box-shadow: 0 4px 15px rgba(61, 220, 132, 0.3); transition: all 0.3s ease;">
                        <i class="fab fa-android mr-3" style="font-size: 1.2rem;"></i>
                        <span style="font-size: 1.1rem; font-weight: bold;">Descarga el APK</span>
                        <i class="fas fa-download ml-3"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(61, 220, 132, 0.4) !important;
}
</style>

@endsection
