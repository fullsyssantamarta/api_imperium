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
                                    <a class="dropdown-item text-secondary" href="#" data-toggle="modal" data-target="#editCompanyModal"
                                        data-company-id="{{ $row->id }}"
                                        data-identification-number="{{ $row->identification_number }}"
                                        data-dv="{{ $row->dv }}"
                                        data-type-document-identification-id="{{ $row->type_document_identification_id }}"
                                        data-type-regime-id="{{ $row->type_regime_id }}"
                                        data-type-liability-id="{{ $row->type_liability_id }}"
                                        data-municipality-id="{{ $row->municipality_id }}"
                                        data-merchant-registration="{{ $row->merchant_registration }}"
                                        data-address="{{ $row->address }}"
                                        data-phone="{{ $row->phone }}"
                                        data-api-token="{{ $row->user->api_token }}">
                                        Editar Empresa
                                    </a>
                                    <div class="dropdown-divider"></div>
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

<!-- Modal de Edición de Empresa -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" role="dialog" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCompanyModalLabel">Editar Empresa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="companyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="company-info-tab" data-toggle="tab" href="#company-info" role="tab" aria-controls="company-info" aria-selected="true">
                            <i class="fas fa-building mr-2"></i>Información de la Empresa
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="company-logo-tab" data-toggle="tab" href="#company-logo" role="tab" aria-controls="company-logo" aria-selected="false">
                            <i class="fas fa-image mr-2"></i>Logo de la Empresa
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content" id="companyTabContent">
                    <!-- Tab de Información de la Empresa -->
                    <div class="tab-pane fade show active" id="company-info" role="tabpanel" aria-labelledby="company-info-tab">
                        <form id="editCompanyForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="identification_number">Número de identificación</label>
                                    <input type="text" name="identification_number" id="identification_number" class="form-control" required>
                                    <div class="invalid-feedback"></div>
                                    <small class="form-text text-muted">Solo números. El DV se calculará automáticamente.</small>
                                </div>

                                <div class="form-group col-6">
                                    <label for="dv">Dígito de verificación</label>
                                    <input type="text" name="dv" id="dv" class="form-control" readonly style="background-color: #f8f9fa;">
                                    <div class="invalid-feedback"></div>
                                    <small class="form-text text-muted">Se calcula automáticamente</small>
                                </div>

                                <div class="form-group col-6">
                                    <label for="type_document_identification_id">Tipo de documento</label>
                                    <select name="type_document_identification_id" id="type_document_identification_id" class="form-control" required>
                                        @foreach ($type_document_identifications as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="type_regime_id">Tipo de régimen</label>
                                    <select name="type_regime_id" id="type_regime_id" class="form-control" required>
                                        @foreach ($type_regimes as $regime)
                                            <option value="{{ $regime->id }}">{{ $regime->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="type_liability_id">Tipo de responsabilidad</label>
                                    <select name="type_liability_id" id="type_liability_id" class="form-control" required>
                                        @foreach ($type_liabilities as $liability)
                                            <option value="{{ $liability->id }}">{{ $liability->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="municipality_id">Municipio</label>
                                    <select name="municipality_id" id="municipality_id" class="form-control" required>
                                        @foreach ($municipalities as $municipality)
                                            <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="merchant_registration">Matrícula mercantil</label>
                                    <input type="text" name="merchant_registration" id="merchant_registration" class="form-control">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="phone">Teléfono</label>
                                    <input type="text" name="phone" id="phone" class="form-control">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group col-12">
                                    <label for="address">Dirección</label>
                                    <input type="text" name="address" id="address" class="form-control">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tab de Logo de la Empresa -->
                    <div class="tab-pane fade" id="company-logo" role="tabpanel" aria-labelledby="company-logo-tab">
                        <form id="editLogoForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="logo-upload">Logo de la Empresa</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="logo-upload" accept="image/jpeg">
                                            <label class="custom-file-label" for="logo-upload">Seleccionar archivo...</label>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                        <small class="form-text text-muted">Formato permitido: JPG únicamente. Tamaño máximo: 2MB</small>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div id="logo-preview" class="text-center" style="display: none;">
                                        <h6>Vista previa:</h6>
                                        <img id="logo-preview-img" src="" alt="Vista previa del logo" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" id="saveCompanyBtn" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Información
                </button>
                <button type="button" id="saveLogoBtn" class="btn btn-success" style="display: none;">
                    <i class="fas fa-image"></i> Actualizar Logo
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(61, 220, 132, 0.4) !important;
}

.text-danger {
    color: #dc3545 !important;
}

.form-group {
    margin-bottom: 1rem;
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.is-invalid .invalid-feedback {
    display: block;
}

.nav-tabs .nav-link {
    color: #495057;
    border: 1px solid transparent;
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    padding: 1rem;
    border-bottom-left-radius: .25rem;
    border-bottom-right-radius: .25rem;
}

.custom-file-label::after {
    content: "Buscar";
}
</style>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let currentCompanyId = null;
    let currentApiToken = null;

    // Limpiar formulario cuando se abre el modal
    $('#editCompanyModal').on('show.bs.modal', function () {
        clearFormErrors();
        clearLogoForm();
        // Asegurar que el primer tab esté activo
        $('#company-info-tab').tab('show');
    });

    // Configurar el modal para edición de empresa
    $('#editCompanyModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);

        if (button && button.length > 0 && button.data('company-id')) {
            currentCompanyId = button.data('company-id') || '';
            currentApiToken = button.data('api-token') || '';
            var identificationNumber = button.data('identification-number') || '';
            var dv = button.data('dv') || '';
            var typeDocumentIdentificationId = button.data('type-document-identification-id') || '';
            var typeRegimeId = button.data('type-regime-id') || '';
            var typeLiabilityId = button.data('type-liability-id') || '';
            var municipalityId = button.data('municipality-id') || '';
            var merchantRegistration = button.data('merchant-registration') || '';
            var address = button.data('address') || '';
            var phone = button.data('phone') || '';

            modal.find('#identification_number').val(identificationNumber);
            modal.find('#dv').val(dv);
            modal.find('#type_document_identification_id').val(typeDocumentIdentificationId);
            modal.find('#type_regime_id').val(typeRegimeId);
            modal.find('#type_liability_id').val(typeLiabilityId);
            modal.find('#municipality_id').val(municipalityId);
            modal.find('#merchant_registration').val(merchantRegistration);
            modal.find('#address').val(address);
            modal.find('#phone').val(phone);
        }
    });

    // Manejar cambio de tabs
    $('#companyTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");

        if (target === '#company-info') {
            $('#saveCompanyBtn').show();
            $('#saveLogoBtn').hide();
        } else if (target === '#company-logo') {
            $('#saveCompanyBtn').hide();
            $('#saveLogoBtn').show();
        }
    });

    // Manejar subida de archivo de logo
    $('#logo-upload').on('change', function(e) {
        const file = e.target.files[0];
        const label = $(this).next('.custom-file-label');

        if (file) {
            // Validar tipo de archivo
            const allowedTypes = ['image/jpg', 'image/jpeg'];
            if (!allowedTypes.includes(file.type)) {
                new PNotify({
                    text: 'Por favor selecciona un archivo JPG válido',
                    type: 'error',
                    addclass: 'notification-danger',
                    delay: 4000
                });
                $(this).val('');
                label.text('Seleccionar archivo...');
                $('#logo-preview').hide();
                return;
            }

            // Validar tamaño (2MB máximo)
            if (file.size > 2 * 1024 * 1024) {
                new PNotify({
                    text: 'El archivo es demasiado grande. Tamaño máximo: 2MB',
                    type: 'error',
                    addclass: 'notification-danger',
                    delay: 4000
                });
                $(this).val('');
                label.text('Seleccionar archivo...');
                $('#logo-preview').hide();
                return;
            }

            // Actualizar label
            label.text(file.name);

            // Mostrar vista previa
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logo-preview-img').attr('src', e.target.result);
                $('#logo-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            label.text('Seleccionar archivo...');
            $('#logo-preview').hide();
        }
    });

    // Manejar envío del formulario de información de empresa
    $('#saveCompanyBtn').on('click', function(e) {
        e.preventDefault();

        const submitBtn = $(this);
        const originalText = submitBtn.html();

        // Deshabilitar botón y mostrar estado de carga
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        // Limpiar errores previos
        clearFormErrors();

        // Preparar datos del formulario
        const formData = $('#editCompanyForm').serialize();

        // Realizar petición AJAX
        $.ajax({
            url: `/companies/${currentCompanyId}`,
            method: 'PUT',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Mostrar notificación de éxito
                    new PNotify({
                        text: response.message || 'Empresa actualizada exitosamente',
                        type: 'success',
                        addclass: 'notification-success',
                        delay: 3000
                    });

                    // Cerrar modal
                    $('#editCompanyModal').modal('hide');

                    // Recargar página para mostrar los cambios
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    // Mostrar notificación de error
                    new PNotify({
                        text: response.message || 'Error al actualizar la empresa',
                        type: 'error',
                        addclass: 'notification-danger',
                        delay: 5000
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Errores de validación
                    const errors = xhr.responseJSON.errors;
                    displayFormErrors(errors);

                    // Cambiar al tab de información si hay errores
                    $('#company-info-tab').tab('show');

                    new PNotify({
                        text: 'Por favor corrige los errores en el formulario',
                        type: 'error',
                        addclass: 'notification-danger',
                        delay: 5000
                    });
                } else {
                    // Otros errores
                    const message = xhr.responseJSON?.message || 'Error interno del servidor';
                    new PNotify({
                        text: message,
                        type: 'error',
                        addclass: 'notification-danger',
                        delay: 5000
                    });
                }
            },
            complete: function() {
                // Restaurar botón
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Manejar envío del logo
    $('#saveLogoBtn').on('click', function(e) {
        e.preventDefault();

        const file = $('#logo-upload')[0].files[0];
        if (!file) {
            new PNotify({
                text: 'Por favor selecciona una imagen',
                type: 'error',
                addclass: 'notification-danger',
                delay: 3000
            });
            return;
        }

        const submitBtn = $(this);
        const originalText = submitBtn.html();

        // Deshabilitar botón y mostrar estado de carga
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo logo...');

        // Convertir archivo a base64
        const reader = new FileReader();
        reader.onload = function(e) {
            const base64Data = e.target.result.split(',')[1]; // Remover el prefijo data:image/...;base64,

            // Realizar petición AJAX al API
            $.ajax({
                url: '/api/ubl2.1/config/logo',
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + currentApiToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify({
                    logo: base64Data
                }),
                success: function(response) {
                    new PNotify({
                        text: 'Logo actualizado exitosamente',
                        type: 'success',
                        addclass: 'notification-success',
                        delay: 3000
                    });

                    // Limpiar el formulario de logo
                    clearLogoForm();
                },
                error: function(xhr) {
                    let message = 'Error al actualizar el logo';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    new PNotify({
                        text: message,
                        type: 'error',
                        addclass: 'notification-danger',
                        delay: 5000
                    });
                },
                complete: function() {
                    // Restaurar botón
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        };
        reader.readAsDataURL(file);
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

    // Función para limpiar errores del formulario
    function clearFormErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();
    }

    // Función para mostrar errores de validación
    function displayFormErrors(errors) {
        $.each(errors, function(field, messages) {
            const input = $(`#${field}`);
            const feedback = input.siblings('.invalid-feedback');

            input.addClass('is-invalid');
            feedback.text(messages[0]).show();
        });
    }

    // Función para limpiar el formulario de logo
    function clearLogoForm() {
        $('#logo-upload').val('');
        $('.custom-file-label').text('Seleccionar archivo...');
        $('#logo-preview').hide();
    }

    // Función para calcular el dígito de verificación
    function calculateDV(nit) {
        if (!nit || nit.length === 0) return '';

        var sequence = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
        var sum = 0;
        var nitArray = nit.toString().split('').reverse();

        for (var i = 0; i < nitArray.length && i < sequence.length; i++) {
            sum += parseInt(nitArray[i]) * sequence[i];
        }

        var remainder = sum % 11;
        var dv = remainder > 1 ? 11 - remainder : remainder;

        return dv.toString();
    }

    // Calcular DV automáticamente cuando se escriba el número de identificación
    $('#identification_number').on('input', function() {
        var nit = $(this).val().replace(/\D/g, ''); // Solo números
        if (nit.length > 0) {
            var dv = calculateDV(nit);
            $('#dv').val(dv);
        } else {
            $('#dv').val('');
        }
    });

    // También permitir solo números en el campo de identificación
    $('#identification_number').on('keypress', function(e) {
        var char = String.fromCharCode(e.which);
        if (!/[0-9]/.test(char)) {
            e.preventDefault();
        }
    });

    // Solo permitir números en el campo DV
    $('#dv').on('keypress', function(e) {
        var char = String.fromCharCode(e.which);
        if (!/[0-9]/.test(char)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
