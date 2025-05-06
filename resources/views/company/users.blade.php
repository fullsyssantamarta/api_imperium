@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>Listado de Usuarios</h2>
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
            <div class="col text-right">
                <button class="btn btn-primary mb-0" data-toggle="modal" data-target="#userModal">Añadir usuario</button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Documento</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->document_type_id ? $user->document_type->name : '' }} {{ $user->document_number }}</td>
                    <td class="text-right">
                        <button class="btn btn-sm btn-warning"
                            data-toggle="modal" data-target="#userModal"
                            data-id="{{ $user->id }}"
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-document_number="{{ $user->document_number }}"
                            data-document_type_id="{{ $user->document_type_id }}"
                            data-can_rips="{{ $user->can_rips }}"
                            data-can_health="{{ $user->can_health }}"
                            data-code_service_provider="{{ $user->code_service_provider}}">Editar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="userForm" method="POST" action="{{ route('company.users.store', $company->id) }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="userId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Formulario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <!-- Mostrar errores de validación -->
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="form-group col-6">
                            <label for="document_type_id">Tipo de documento</label>
                            <select name="document_type_id" id="document_type_id" class="form-control" required>
                                @foreach ($document_types as $documentType)
                                <option value="{{ $documentType->id }}"
                                    {{ old('document_type_id') == $documentType->id ? 'selected' : '' }}>
                                    {{ $documentType->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="document_number">Número de documento</label>
                            <input type="text" name="document_number" id="document_number" class="form-control" value="{{ old('document_number') }}" required>
                        </div>

                        <div class="form-group col-6">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                        <div class="form-group col-6">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                        </div>
                        <div class="form-group col-6">
                            <label for="code_service_provider">Código de prestador de servicio</label>
                            <input type="text" name="code_service_provider" id="code_service_provider" class="form-control" value="{{ old('code_service_provider') }}">
                            <span class="text-muted"><small>Código de 12 digitos</small></span>
                        </div>
                        <div class="form-group col-6 mt-4">
                            <input type="checkbox" value="1" name="can_rips" id="can_rips" {{ old('can_rips') ? 'checked' : '' }}>
                            <label for="can_rips">Generar RIPS</label>
                            <br>
                            <input type="checkbox" value="1" name="can_health" id="can_health" {{ old('can_health') ? 'checked' : '' }}>
                            <label for="can_health">Generar Factura de Sector Salud</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Abrir el modal automáticamente si hay errores de validación
    @if ($errors->any())
        $('#userModal').modal('show');
    @endif

    // Configurar el modal para edición o creación
    $('#userModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id') || '';
        var name = button.data('name') || '';
        var email = button.data('email') || '';
        var can_rips = button.data('can_rips') || false;
        var can_health = button.data('can_health') || false;
        var code_service_provider = button.data('code_service_provider') || '';
        var document_type_id = button.data('document_type_id') || '';
        var document_number = button.data('document_number') || '';

        var modal = $(this);
        modal.find('.modal-title').text(id ? 'Editar Usuario' : 'Agregar Usuario');
        modal.find('#userId').val(id);
        modal.find('#name').val(name);
        modal.find('#email').val(email);
        modal.find('#code_service_provider').val(code_service_provider);
        modal.find('#document_type_id').val(document_type_id);
        modal.find('#document_number').val(document_number);

        // Set checkboxes
        modal.find('#can_rips').prop('checked', can_rips);
        modal.find('#can_health').prop('checked', can_health);

        if (id) {
            modal.find('#formMethod').val('PUT');
            modal.find('#userForm').attr('action', '/companies/{{ $company->id }}/users/' + id);
        } else {
            modal.find('#formMethod').val('POST');
            modal.find('#userForm').attr('action', '{{ route('company.users.store', $company->id) }}');
        }
    });

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