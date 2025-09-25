@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Paso a Producción</h2>
</header>

<div class="card border">
    <div class="card-body">
        <form id="productionForm" method="POST" action="{{ route('company.production.process', $company) }}">
            @csrf
            <div class="form-group">
                <h3>Software de Facturación Electrónica</h3>
                <label for="test_set_id">Set de Pruebas DIAN</label>
                <input type="text" class="form-control" id="test_set_id" name="test_set_id" required placeholder="Ingrese el TestSetId entregado por la DIAN">
            </div>
            <button type="submit" class="btn btn-success mt-3">Iniciar Paso a Producción</button>
        </form>

@if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
        @if(session('zipkey'))
            <div><strong>ZipKey:</strong> {{ session('zipkey') }}</div>
        @endif
        @if(session('zipkey_status'))
            <div class="mt-2">
                <strong>Estado ZipKey:</strong>
                <pre style="white-space: pre-wrap;">{{ print_r(session('zipkey_status'), true) }}</pre>
            </div>
        @endif
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
@endif

{{-- Nuevo div para el botón y resultado de resoluciones --}}
@if($isProduction)
        <div class="card border mt-4">
            <div class="card-body">
                <h3 class="mb-3">Consultar Resoluciones Asociadas</h3>
                <button id="btnConsultarResoluciones" type="button" class="btn btn-primary">Consultar</button>
                <div id="resolucionesResult" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
@endif
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btnConsultarResoluciones');
    if (btn) {
        btn.addEventListener('click', function() {
            const resultDiv = document.getElementById('resolucionesResult');
            resultDiv.innerHTML = 'Consultando...';
            fetch("{{ route('company.consultar.resoluciones', $company->identification_number) }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.innerHTML = '<pre style="white-space: pre-wrap;">' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                resultDiv.innerHTML = '<span class="text-danger">Error consultando resoluciones</span>';
            });
        });
    }
});
</script>
@endpush
@endsection