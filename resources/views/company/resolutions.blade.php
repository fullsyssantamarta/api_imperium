@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Listado de Resoluciones</h2>
</header>

<div class="card border">
    <div class="card-header ">
        <div class="row no-wrapper">
            <div class="col" style="line-height: 1rem;">
                {{ $company->user->name }} <br>
                <small>
                    {{ $company->user->email }}<br>
                    {{ $company->identification_number }}-{{ $company->dv }}
                </small>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Entorno</th>
                    <th>Prefijo</th>
                    <th>Número</th>
                    <th>Tipo de Documento</th>
                    <th>Fecha</th>
                    <th>Rango</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Clave Técnica</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resolutions as $resolution)
                @php
                    $environtment = 'N/A';
                    switch ($resolution->type_environment_id) {
                        case '2':
                            $environtment = 'Habilitación';
                            break;
                        case '1':
                            $environtment = 'Producción';
                            break;
                        default:
                            $environtment = 'N/A';
                            break;
                    }
                @endphp
                <tr>
                    <td>{{ $environtment }}</td>
                    <td>{{ $resolution->prefix }}</td>
                    <td>{{ $resolution->number }}</td>
                    <td>{{ $resolution->type_document->name }}</td>
                    <td>{{ $resolution->resolution_date }}</td>
                    <td>{{ $resolution->from }} - {{ $resolution->to }}</td>
                    <td>{{ $resolution->date_from }}</td>
                    <td>{{ $resolution->date_to }}</td>
                    <td>{{ $resolution->technical_key }}</td>
                    <td class="text-right">
                        <form action="{{ route('company.resolutions.update', ['resolution' => $resolution->id, 'company' => $company->identification_number]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-warning">Actualizar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-12 d-flex justify-content-end pr-4">
            {{ $resolutions->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Mostrar mensaje de éxito
    @if (session('success'))
        new PNotify({
            text: '{{ session('success') }}',
            type: 'success',
            addclass: 'notification-success',
            delay: 3000
        });
    @endif

    // Mostrar mensaje de error
    @if (session('error'))
        new PNotify({
            text: '{{ session('error') }}',
            type: 'error',
            addclass: 'notification-danger',
            delay: 3000
        });
    @endif
});
</script>
@endpush