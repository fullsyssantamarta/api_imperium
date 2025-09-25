@extends('layouts.app')
@section('content')
<header class="page-header d-flex justify-content-between align-items-center">
    <h2>Documentos generados</h2>
    <div>
        <button class="btn btn-primary btn-lg shadow-sm" data-toggle="modal" data-target="#excelModal">
            <i class="fas fa-upload mr-2"></i>Subida Masiva
        </button>
    </div>
</header>

@php
    // dd($resolution_credit_notes);
@endphp


<div class="card border">
    <div class="table-responsive card-body p-0">
        <table class="table table-sm table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Acciones</th>
                    <th>DIAN</th>
                    <th>Descargas</th>
                    <th>Ambiente</th>
                    <th>Válido</th>
                    <th>Fecha</th>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Tipo de Documento</th>
                    <th class="text-right">Impuesto</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $row)
                    <tr class="table-light">
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($row->type_document_id == 1 && $row->response_dian && $resolution_credit_notes && count($resolution_credit_notes) > 0)
                                @php
                                    $isValidResponse = false;
                                    if ($row->response_dian) {
                                        $decodedResponse = json_decode($row->response_dian, true);
                                        $isValidResponse = isset($decodedResponse['Envelope']['Body']['SendBillSyncResponse']['SendBillSyncResult']['IsValid'])
                                            && $decodedResponse['Envelope']['Body']['SendBillSyncResponse']['SendBillSyncResult']['IsValid'] === 'true';
                                    }
                                @endphp
                                @if($isValidResponse)
                                    <button type="button" class="btn btn-info btn-xs btn-credit-note mt-0"
                                        data-id="{{ $row->id }}"
                                        data-cufe="{{ $row->cufe }}"
                                        data-request-api="{{ $row->request_api }}">
                                        Nota de crédito
                                    </button>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($row->response_dian)
                                <button type="button" class="btn btn-primary btn-xs modalApiResponse"
                                    data-content="{{ $row->response_dian }}">
                                    Respuesta DIAN
                                </button>
                                <br>
                            @endif
                            @if($row->cufe)
                                <button type="button" class="btn btn-primary btn-xs makeApiRequest mt-1"
                                    data-id="{{ $row->cufe }}">
                                    CUFE
                                </button>
                                <br>
                            @endif
                            @if(!$row->state_document_id)
                                <button type="button" class="btn btn-primary btn-xs modalChangeState mt-1"
                                    data-id="{{ $row->id }}">
                                    ESTADO
                                </button>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-success btn-xs text-white"
                                role="button"
                                href="{{ '/storage/'.$row->identification_number.'/'.$row->xml }}" target="_BLANK">
                                XML
                            </a>
                            <a class="btn btn-success btn-xs text-white mt-1"
                                role="button"
                                href="{{ '/storage/'.$row->identification_number.'/'.$row->pdf }}" target="_BLANK">
                                PDF
                            </a>
                        </td>
                        <td>{{ $row->ambient_id === 2 ? 'Habilitación' : 'Producción' }}</td>
                        <td class="text-center">{{ $row->state_document_id ? 'Si' : 'No' }}</td>
                        <td>{{ $row->date_issue }}</td>
                        <td>{{ $row->prefix }}{{ $row->number }}</td>
                        <td>
                            @inject('typeDocuments', 'App\TypeDocumentIdentification')
                            @php
                                $doc_id = $row->client->type_document_identification_id ?? null;
                                $document_type = $typeDocuments->where('id', $doc_id)->first() ?? null;
                                // dd($document_type);
                            @endphp
                            {{-- @if(!$document_type)
                                {{dd($row->client)}}
                            @endif --}}
                            {{ $row->client->name ?? 'Sin nombre' }}<br>
                            {{ $document_type != null ? $document_type->name : '' }} {{ $row->client->identification_number ?? 'sin identificación' }}-{{ $row->client->dv ?? ""}}</td>
                        <td>{{ $row->type_document->name }}</td>
                        <td class="text-right">{{ round($row->total_tax, 2) }}</td>
                        <td class="text-right">{{ round($row->subtotal, 2) }}</td>
                        <td class="text-right">{{ round($row->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- {{ dd($documents) }} --}}
    </div>
    <div class="card-footer d-flex justify-content-center">
        {{ $documents->links() }}
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Consulta de CUFE</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="modalBodyContent"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Respuesta dada por el API</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="modalBodyResponse"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="changeStateModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Cambio de Estado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">Esto cambiará el estado del documento en este listado del API, es importante que se verifique el <strong>CUFE</strong> en la DIAN donde se muestre como ACEPTADO para continuar con este procedimiento.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <form action="{{ route('document.change-state') }}" method="POST">
                    @csrf
                    <input type="hidden" name="document_id" id="verificarInput" value=""/>
                    <button type="submit" class="btn btn-success">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excel a JSON -->
<div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="excelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h5 class="modal-title mr-3" id="excelModalLabel">
                    <i class="fas fa-upload mr-2"></i>Subida Masiva de Facturas
                </h5>
                <a href="{{ asset('xlsx/co-documents-batch.xlsx') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download mr-1"></i>Descargar Plantilla
                </a>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="excelFile" class="font-weight-bold d-block">
                        <i class="fas fa-upload mr-2"></i>Archivo Excel
                    </label>
                    <input type="file" class="form-control-file" id="excelFile" accept=".xls,.xlsx">
                </div>
                <div class="progress mt-3 d-none" id="progressBar">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                </div>
                <div class="card border mt-4">
                    <div class="card-header">
                        <i class="fas fa-list-alt mr-2"></i>Resultado del Procesamiento
                    </div>
                    <div id="apiResults" class="card-body bg-light" style="max-height: 300px; overflow-y: auto; font-family: monospace;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-success d-none" id="finishProcess" onclick="location.reload()">
                    <i class="fas fa-check mr-2"></i>Finalizar
                </button>
                <button type="button" class="btn btn-primary" id="processInvoices">
                    <i class="fas fa-cogs mr-2"></i>Procesar Facturas
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selección de Resolución para Nota de Crédito -->
<div class="modal fade" id="resolutionModal" tabindex="-1" role="dialog" aria-labelledby="resolutionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resolutionModalLabel">
                    <i class="fas fa-file-invoice mr-2"></i>Seleccionar Resolución para Nota de Crédito
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Seleccione la resolución que desea utilizar para generar la nota de crédito.
                </div>
                <div class="list-group" id="resolutionList">
                    @if($resolution_credit_notes)
                        @foreach($resolution_credit_notes as $resolution)
                            <button type="button" class="list-group-item list-group-item-action resolution-item"
                                data-resolution-id="{{ $resolution->id }}"
                                data-resolution-prefix="{{ $resolution->prefix }}"
                                data-resolution-number="{{ $resolution->resolution_number ?? $resolution->resolution ?? '' }}"
                                data-resolution-has-number="{{ ($resolution->resolution_number ?? $resolution->resolution) ? 'true' : 'false' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $resolution->prefix }}</h6>
                                    <small>{{ $resolution->type_document->name ?? 'Nota de Crédito' }}</small>
                                </div>
                                <p class="mb-1">
                                    <strong>Resolución:</strong>
                                    @if($resolution->resolution_number ?? $resolution->resolution)
                                        {{ $resolution->resolution_number ?? $resolution->resolution }}
                                    @else
                                        <span class="text-danger">Sin número de resolución</span>
                                    @endif
                                </p>
                                <small>
                                    <strong>Rango:</strong> {{ $resolution->from }} - {{ $resolution->to }}
                                    @if($resolution->date_from && $resolution->date_to)
                                        | <strong>Vigencia:</strong> {{ $resolution->date_from }} - {{ $resolution->date_to }}
                                    @endif
                                </small>
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Añadir función para copiar token
    window.copyToken = function() {
        const tokenText = document.getElementById('apiToken').textContent;
        navigator.clipboard.writeText(tokenText).then(() => {
            alert('Token copiado al portapapeles');
        }).catch(err => {
            console.error('Error al copiar token:', err);
            alert('Error al copiar token');
        });
    };

    // Variable global para almacenar los datos
    window.transformedData = []; // Cambiamos a window.transformedData para acceso global

    $('.makeApiRequest').click(function() {
        var cufe = $(this).data('id');
        var $button = $(this);
        $button.prop('disabled', true);

        $.ajax({
            url: '{{ url('/company/'.$company->identification_number.'/document/') }}/' + cufe,
            method: 'GET',
            success: function(response) {
                // Mostrar la respuesta en el modal
                $('#modalBodyContent').html(JSON.stringify(response, null, 2));
                $('#resultModal').modal('show');
            },
            error: function(xhr) {
                // Manejar errores
                $('#modalBodyContent').html('Ocurrió un error: ' + xhr.status + ' ' + xhr.statusText);
                $('#resultModal').modal('show');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    $('.modalApiResponse').click(function() {
        var content = $(this).data('content');
        $('#modalBodyResponse').html(JSON.stringify(content, null, 2));
        $('#responseModal').modal('show');
    });
    $('.modalChangeState').click(function() {
        var id = $(this).data('id');
        $('#verificarInput').val(id);
        $('#changeStateModal').modal('show');
    });

    // Variable global para almacenar los datos del documento actual
    window.currentDocumentData = null;
    window.currentButton = null;

    // Manejar clic en botón "Nota de crédito"
    $(document).on('click', '.btn-credit-note', function() {
        var documentId = $(this).data('id');
        var $button = $(this);

        // Buscar la fila correspondiente para obtener los datos del documento
        var $row = $button.closest('tr');
        var documentData = {
            id: documentId,
            prefix: $row.find('td:nth-child(8)').text().match(/^([A-Z]+)/)?.[1] || '', // Extraer prefijo del número
            number: $row.find('td:nth-child(8)').text().match(/\d+/)?.[0] || '', // Extraer número
            cufe: $button.data('cufe'),
            date_issue: $row.find('td:nth-child(7)').text().split(' ')[0], // Extraer solo la fecha sin la hora
            request_api: $button.data('request-api')
        };

        // Guardar los datos globalmente
        window.currentDocumentData = documentData;
        window.currentButton = $button;

        // Mostrar el modal de selección de resolución
        $('#resolutionModal').modal('show');
    });

    // Manejar selección de resolución
    $(document).on('click', '.resolution-item', function() {
        var resolutionId = $(this).data('resolution-id');
        var resolutionPrefix = $(this).data('resolution-prefix');
        var resolutionNumber = $(this).data('resolution-number');
        var hasResolutionNumber = $(this).data('resolution-has-number') === 'true';

        console.log('Datos de resolución:', {
            id: resolutionId,
            prefix: resolutionPrefix,
            number: resolutionNumber,
            hasNumber: hasResolutionNumber
        });

        // Verificar si la resolución tiene número (más flexible)
        if (!resolutionNumber || resolutionNumber === 'undefined') {
            new PNotify({
                text: 'Esta resolución no tiene configurado el número de resolución. Por favor, agregue este campo en el menú de resoluciones.',
                type: 'warning',
                addclass: 'notification-warning',
                delay: 5000
            });
            return;
        }

        // Procesar la nota de crédito con la resolución seleccionada
        if (window.currentDocumentData && window.currentButton) {
            // Deshabilitar botón mientras se procesa
            window.currentButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

            // Deshabilitar todos los botones del modal y mostrar estado de procesando
            $('.resolution-item').prop('disabled', true);
            $('#resolutionModal .btn-secondary').prop('disabled', true);
            $('#resolutionModalLabel').html('<i class="fas fa-spinner fa-spin mr-2"></i>Procesando Nota de Crédito...');

            // Cambiar el contenido del modal para mostrar progreso
            $('#resolutionModal .modal-body').html(`
                <div class="alert alert-info">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Procesando la nota de crédito con la resolución <strong>${resolutionPrefix}</strong>...
                </div>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%">
                        Enviando a la DIAN...
                    </div>
                </div>
            `);

            processCreditNote(window.currentDocumentData, window.currentButton, {
                id: resolutionId,
                prefix: resolutionPrefix,
                resolution_number: resolutionNumber
            });
        }
    });

    // Limpiar variables globales cuando se cierre el modal
    $('#resolutionModal').on('hidden.bs.modal', function () {
        window.currentDocumentData = null;
        window.currentButton = null;
    });

    // Función para procesar la nota de crédito
    async function processCreditNote(documentData, $button, selectedResolution) {
        try {
            const token = '{{ $token_company }}';

            if (!selectedResolution) {
                throw new Error('No se encontró resolución seleccionada para notas de crédito');
            }

            const payloadConsecutive = {
                type_document_id: 4,
                prefix: selectedResolution.prefix
            };

            // 1. Consultar next-consecutive
            const consecutiveResponse = await fetch('/api/ubl2.1/next-consecutive', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(payloadConsecutive)
            });

            if (!consecutiveResponse.ok) {
                throw new Error('Error al obtener el consecutivo');
            }

            const consecutiveData = await consecutiveResponse.json();
            const nextNumber = consecutiveData.number; // Convertir a string para prueba

            // 2. Armar el JSON para la nota de crédito
            console.log('Datos request_api:', documentData.request_api);
            console.log('Tipo de request_api:', typeof documentData.request_api);

            const originalData = documentData.request_api; // Ya viene como objeto, no parsear
            const now = new Date();
            // Ajustar a zona horaria de Colombia (GMT-5)
            const colombiaTime = new Date(now.getTime() - (5 * 60 * 60 * 1000));
            const currentDate = colombiaTime.toISOString().split('T')[0];
            const currentTime = colombiaTime.toTimeString().split(' ')[0];

            const creditNoteData = {
                billing_reference: {
                    number: documentData.prefix + documentData.number,
                    uuid: documentData.cufe,
                    issue_date: documentData.date_issue
                },
                resolution_number: selectedResolution.resolution_number,
                discrepancyresponsecode: 2,
                discrepancyresponsedescription: "NOTA DE CREDITO GENERADA AUTOMATICAMENTE",
                notes: "NOTA DE CREDITO",
                prefix: selectedResolution.prefix,
                number: nextNumber,
                type_document_id: 4,
                date: currentDate,
                time: currentTime,
                sendmail: originalData.sendmail || false,
                sendmailtome: originalData.sendmailtome || false,
                seze: "2021-2017",
                head_note: originalData.head_note || '',
                foot_note: originalData.foot_note || '',
                customer: originalData.customer,
                tax_totals: originalData.tax_totals,
                legal_monetary_totals: originalData.legal_monetary_totals,
                credit_note_lines: originalData.invoice_lines.map(line => ({
                    unit_measure_id: line.unit_measure_id,
                    invoiced_quantity: line.invoiced_quantity,
                    line_extension_amount: line.line_extension_amount,
                    free_of_charge_indicator: line.free_of_charge_indicator,
                    tax_totals: line.tax_totals,
                    description: line.description,
                    notes: line.notes || '',
                    code: line.code,
                    type_item_identification_id: line.type_item_identification_id,
                    price_amount: line.price_amount,
                    base_quantity: line.base_quantity
                }))
            };
            console.log('Datos de la nota de crédito a enviar:', creditNoteData);
            // 3. Enviar la nota de crédito
            const creditNoteResponse = await fetch('/api/ubl2.1/credit-note', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(creditNoteData)
            });

            const creditNoteResult = await creditNoteResponse.json();

            // 4. Verificar el resultado y mostrar notificación
            const statusCode = creditNoteResult.ResponseDian?.Envelope?.Body?.SendBillSyncResponse?.SendBillSyncResult?.StatusCode;

            if (statusCode === "00") {
                // Mostrar resultado exitoso en el modal
                $('#resolutionModal .modal-body').html(`
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>¡Nota de crédito creada exitosamente!</strong>
                    </div>
                    <p>La nota de crédito ha sido enviada correctamente a la DIAN.</p>
                `);
                $('#resolutionModalLabel').html('<i class="fas fa-check-circle mr-2"></i>Nota de Crédito Creada');

                // Cambiar botón de cancelar por cerrar
                $('#resolutionModal .btn-secondary').removeClass('disabled').prop('disabled', false)
                    .html('<i class="fas fa-times mr-2"></i>Cerrar').off('click').on('click', function() {
                        location.reload();
                    });

                new PNotify({
                    text: 'Nota de crédito creada exitosamente',
                    type: 'success',
                    addclass: 'notification-success',
                    delay: 3000
                });
            } else {
                const errorMessage = creditNoteResult.ResponseDian?.Envelope?.Body?.SendBillSyncResponse?.SendBillSyncResult?.ErrorMessage?.string || 'Error desconocido';

                // Mostrar error en el modal
                $('#resolutionModal .modal-body').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Error al crear la nota de crédito</strong>
                    </div>
                    <p><strong>Mensaje de error:</strong></p>
                    <div class="bg-light p-3 rounded">
                        <small>${errorMessage}</small>
                    </div>
                `);
                $('#resolutionModalLabel').html('<i class="fas fa-times-circle mr-2"></i>Error en Nota de Crédito');

                // Habilitar botón de cerrar
                $('#resolutionModal .btn-secondary').removeClass('disabled').prop('disabled', false)
                    .html('<i class="fas fa-times mr-2"></i>Cerrar');

                new PNotify({
                    text: 'Error al crear la nota de crédito: ' + errorMessage,
                    type: 'error',
                    addclass: 'notification-danger',
                    delay: 5000
                });
            }

        } catch (error) {
            console.error('Error procesando nota de crédito:', error);

            // Mostrar error en el modal
            $('#resolutionModal .modal-body').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Error al procesar la nota de crédito</strong>
                </div>
                <p><strong>Mensaje de error:</strong></p>
                <div class="bg-light p-3 rounded">
                    <small>${error.message}</small>
                </div>
            `);
            $('#resolutionModalLabel').html('<i class="fas fa-exclamation-triangle mr-2"></i>Error de Conexión');

            // Habilitar botón de cerrar
            $('#resolutionModal .btn-secondary').removeClass('disabled').prop('disabled', false)
                .html('<i class="fas fa-times mr-2"></i>Cerrar');

            new PNotify({
                text: 'Error al procesar la nota de crédito: ' + error.message,
                type: 'error',
                addclass: 'notification-danger',
                delay: 5000
            });
        } finally {
            // Restaurar el botón
            $button.prop('disabled', false).html('Nota de crédito');
        }
    }

    // Manejo de Excel a JSON
    $('#excelFile').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array',
                    cellDates: true,
                    dateNF: 'yyyy-mm-dd'
                });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet, {
                    raw: false,
                    dateNF: 'yyyy-mm-dd'
                });

                if (jsonData.length === 0) {
                    alert('El archivo Excel está vacío');
                    return;
                }

                const transformedData = [];
                const invoiceGroups = {};

                // Primero agrupamos por número de factura
                jsonData.forEach(row => {
                    if (!invoiceGroups[row.number]) {
                        invoiceGroups[row.number] = {
                            header: row,
                            lines: [],
                            totalDiscount: parseFloat(row.discount_amount) || 0
                        };
                    }
                    // Agregamos la línea de producto sin descuento
                    invoiceGroups[row.number].lines.push({
                        unit_measure_id: parseInt(row.line_unit_measure_id),
                        invoiced_quantity: row.line_invoiced_quantity.toString(),
                        line_extension_amount: formatDecimal(row.line_extension_amount),
                        free_of_charge_indicator: false,
                        allowance_charges: [{
                            charge_indicator: false,
                            allowance_charge_reason: "DESCUENTO GENERAL",
                            amount: "0.00",
                            base_amount: formatDecimal(row.line_extension_amount)
                        }],
                        tax_totals: [{
                            tax_id: parseInt(row.line_tax_tax_id),
                            tax_amount: formatDecimal(row.line_tax_tax_amount),
                            taxable_amount: formatDecimal(row.line_tax_taxable_amount),
                            percent: row.line_tax_percent.toString()
                        }],
                        description: row.line_description,
                        notes: row.line_notes || "",
                        code: row.line_code,
                        type_item_identification_id: parseInt(row.line_type_item_identification_id),
                        price_amount: formatDecimal(row.line_price_amount),
                        base_quantity: row.line_base_quantity.toString()
                    });
                });

                // Luego creamos las facturas con todas sus líneas
                Object.entries(invoiceGroups).forEach(([number, data]) => {
                    const row = data.header;
                    const now = new Date();
                    const currentTime = now.toTimeString().split(' ')[0];

                    // Calcular totales sumando todas las líneas
                    const totals = data.lines.reduce((acc, line) => {
                        const lineAmount = parseFloat(line.line_extension_amount);
                        const taxAmount = parseFloat(line.tax_totals[0].tax_amount);

                        return {
                            line_extension_amount: acc.line_extension_amount + lineAmount,
                            tax_amount: acc.tax_amount + taxAmount,
                            payable_amount: acc.payable_amount + lineAmount + taxAmount
                        };
                    }, { line_extension_amount: 0, tax_amount: 0, payable_amount: 0 });

                    // Aplicar el descuento general
                    const totalDiscount = data.totalDiscount || 0;
                    const finalPayableAmount = totals.payable_amount - totalDiscount;

                    transformedData.push({
                        number: parseInt(row.number),
                        type_document_id: parseInt(row.type_document_id),
                        date: formatDate(row.date),
                        time: currentTime,
                        resolution_number: row.resolution_number,
                        prefix: row.prefix,
                        notes: row.notes || "",
                        disable_confirmation_text: true,
                        establishment_name: row.establishment_name,
                        establishment_address: row.establishment_address,
                        establishment_phone: row.establishment_phone ? row.establishment_phone.toString() : "",
                        establishment_municipality: parseInt(row.establishment_municipality),
                        establishment_email: row.establishment_email,
                        sendmail: true,
                        sendmailtome: true,
                        seze: "2021-2017",
                        head_note: row.head_note || "",
                        foot_note: row.foot_note || "",
                        customer: {
                            identification_number: parseInt(row.customer_identification_number),
                            dv: parseInt(row.customer_dv),
                            name: row.customer_name,
                            phone: row.customer_phone ? row.customer_phone.toString() : "",
                            address: row.customer_address,
                            email: row.customer_email,
                            merchant_registration: row.customer_merchant_registration || "0000000-00",
                            type_document_identification_id: parseInt(row.customer_type_document_identification_id),
                            type_organization_id: parseInt(row.customer_type_organization_id),
                            type_liability_id: parseInt(row.customer_type_liability_id),
                            municipality_id: parseInt(row.customer_municipality_id),
                            type_regime_id: parseInt(row.customer_type_regime_id)
                        },
                        payment_form: {
                            payment_form_id: parseInt(row.payment_form_id),
                            payment_method_id: 10,
                            payment_due_date: formatDate(row.payment_due_date),
                            duration_measure: row.duration_measure ? row.duration_measure.toString() : "0"
                        },
                        legal_monetary_totals: {
                            line_extension_amount: formatDecimal(totals.line_extension_amount),
                            tax_exclusive_amount: formatDecimal(totals.line_extension_amount),
                            tax_inclusive_amount: formatDecimal(totals.payable_amount),
                            allowance_total_amount: formatDecimal(totalDiscount),
                            payable_amount: formatDecimal(finalPayableAmount)
                        },
                        tax_totals: [{
                            tax_id: 1,
                            tax_amount: formatDecimal(totals.tax_amount),
                            percent: "19",
                            taxable_amount: formatDecimal(totals.line_extension_amount)
                        }],
                        invoice_lines: data.lines
                    });
                });

                // Asignar los datos transformados a la variable
                window.transformedData = transformedData;

                $('#apiResults').text('Datos preparados. ' + window.transformedData.length + ' facturas listas para procesar.');
                console.log('Facturas preparadas:', window.transformedData.length);
            } catch (error) {
                console.error('Error procesando el Excel:', error);
                $('#apiResults').text('Error: ' + error.message);
                alert('Error procesando el archivo Excel: ' + error.message);
            }
        };

        reader.onerror = function(ex) {
            console.error('Error leyendo el archivo:', ex);
            $('#apiResults').text('Error leyendo el archivo');
            alert('Error leyendo el archivo Excel');
        };

        reader.readAsArrayBuffer(file);
    });

    // Procesamiento de facturas
    $('#processInvoices').on('click', async function() {
        if (!window.transformedData || window.transformedData.length === 0) {
            alert('No hay facturas para procesar');
            return;
        }

        const $processButton = $(this);
        const $finishButton = $('#finishProcess');
        const progressBar = $('#progressBar');
        const progressBarInner = progressBar.find('.progress-bar');
        const resultsContainer = $('#apiResults');

        // Deshabilitar botón procesar
        $processButton.prop('disabled', true);
        progressBar.removeClass('d-none');
        let results = [];

        for (let i = 0; i < window.transformedData.length; i++) {
            const invoice = window.transformedData[i];
            const progress = ((i + 1) / window.transformedData.length * 100).toFixed(2);

            progressBarInner.css('width', progress + '%')
                          .text(progress + '%');

            try {
                const token = '{{ $company->user->api_token }}';
                if (!token) {
                    throw new Error('No se encontró el token de autenticación');
                }

                const response = await fetch('/api/ubl2.1/invoice', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(invoice)
                });

                const responseData = await response.json();
                const statusCode = responseData.ResponseDian?.Envelope?.Body?.SendBillSyncResponse?.SendBillSyncResult?.StatusCode;
                const errorMessage = responseData.ResponseDian?.Envelope?.Body?.SendBillSyncResponse?.SendBillSyncResult?.ErrorMessage?.string;

                const isSuccess = statusCode === "00";
                results.push({
                    invoice: invoice.number,
                    status: statusCode,
                    message: responseData.message,
                    error: errorMessage,
                    success: isSuccess
                });

                // Mostrar los resultados formateados
                const resultadosFormateados = results.map(r => {
                    let alertClass = 'alert-warning';
                    let icon = 'question-circle';
                    let message = '';

                    if (r.status === "00") {
                        alertClass = 'alert-success';
                        icon = 'check-circle';
                        message = `<div class="text-success">
                            <strong>¡Enviado correctamente!</strong><br>
                            ${r.message}
                        </div>`;
                    } else if (r.status === "99") {
                        alertClass = 'alert-danger';
                        icon = 'times-circle';
                        message = `<div class="text-danger">
                            <strong>Error en el envío:</strong><br>
                            ${r.error}
                        </div>`;
                    }

                    return `
                        <div class="alert ${alertClass} mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-${icon} mr-2"></i>
                                <h6 class="font-weight-bold mb-0">Factura ${r.invoice}</h6>
                            </div>
                            <div class="mt-2">
                                ${message}
                            </div>
                        </div>
                    `;
                }).join('');

                resultsContainer.html(resultadosFormateados);
                resultsContainer.scrollTop(resultsContainer[0].scrollHeight);

                // Delay entre peticiones
                await new Promise(resolve => setTimeout(resolve, 1000));

            } catch (error) {
                console.error('Error procesando factura:', invoice.number, error);
                resultsContainer.append(`
                    <div class="alert alert-danger mb-2">
                        <strong>Error en Factura ${invoice.number}:</strong><br>
                        ${error.message}
                    </div>
                `);
                resultsContainer.scrollTop(resultsContainer[0].scrollHeight);
            }
        }

        // Ocultar barra de progreso
        progressBar.addClass('d-none');
        $finishButton.removeClass('d-none'); // Mostrar botón finalizar

        // Agregar mensaje de completado
        resultsContainer.prepend(`
            <div class="alert alert-info">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Proceso Completado:</strong> Se procesaron ${results.length} facturas.
                <br>
                <small>Haga clic en "Finalizar" para actualizar la lista de documentos.</small>
            </div>
        `);
    });

    function formatDate(dateValue) {
        if (!dateValue) return null;

        console.log('Fecha original:', dateValue); // Para debug

        // Si la fecha viene como string en formato DD/MM/YYYY
        if (typeof dateValue === 'string' && dateValue.includes('/')) {
            const parts = dateValue.split('/');
            if (parts.length === 3) {
                return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
            }
        }

        // Si es una fecha de Excel (número)
        if (!isNaN(dateValue) && typeof dateValue === 'number') {
            const date = new Date((dateValue - 25569) * 86400 * 1000);
            console.log('Fecha convertida de Excel:', date); // Para debug
            return date.toISOString().split('T')[0];
        }

        // Si es una fecha ya formateada YYYY-MM-DD
        if (typeof dateValue === 'string' && dateValue.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return dateValue;
        }

        // Si es un objeto Date
        if (dateValue instanceof Date) {
            return dateValue.toISOString().split('T')[0];
        }

        console.log('No se pudo procesar la fecha:', dateValue); // Para debug
        return dateValue;
    }

    function formatDecimal(number) {
        return number ? Number(number).toFixed(2) : "0.00";
    }
});
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush