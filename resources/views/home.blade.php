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
        <table class="table table-striped table-hover">
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
                                <button class="btn btn-primary text-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item text-secondary" href="{{ route('company', $row->identification_number)}}">Ver documentos</a>
                                    <a class="dropdown-item text-secondary" href="{{ route('company.users.index', $row->id)}}">Usuarios</a>
                                    <a class="dropdown-item text-secondary" href="{{ route('company.email.index', $row->id)}}">Configurar Correo</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
