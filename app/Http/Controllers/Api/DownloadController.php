<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Storage;
use App\Traits\DocumentTrait;
use App\Document;
use App\Municipality;
use App\User;
use App\Company;
use App\PaymentForm;
use App\TypeDocument;
use App\TaxTotal;
use App\PaymentMethod;
use App\HealthField;
use App\Http\Requests\Api\InvoiceRequest;
use Illuminate\Http\Request;
use Exception;


class DownloadController extends Controller
{

    use DocumentTrait;

    public function reloadPdf($identification, $file, $cufe)
    {

        try {
            \Log::info('DownloadController.reloadPdf.START', [
                'identification' => $identification,
                'file' => $file,
                'cufe' => $cufe
            ]);

            // Capturar parámetro de formato de la query string
            $format = request()->query('format');
            if ($format && in_array(strtolower($format), ['letter', 'a4', 'ticket'], true)) {
                config(['pdf.page_format_override' => strtolower($format)]);
                \Log::debug('DownloadController.format_override', [
                    'format' => strtolower($format),
                    'identification' => $identification,
                    'file' => $file
                ]);
            }

            $full_filename = explode('.', $file);

            if($full_filename[1] != 'pdf'){
                \Log::warning('DownloadController.reloadPdf.invalid_file_type');
                return [
                    'success' => false,
                    'message' => 'Tipo de archivo no válido'
                ];
            }

            \Log::info('DownloadController.reloadPdf.searching_document');
            $document = Document::where([['identification_number', $identification], ['pdf', $file]])->firstOrFail();
            \Log::info('DownloadController.reloadPdf.document_found', [
                'document_id' => $document->id,
                'company_id' => $document->company_id
            ]);

            // Obtener usuario: primero intentar autenticado, sino del documento
            $user = auth()->user();
            if (!$user) {
                \Log::info('DownloadController.reloadPdf.no_auth_user_searching_from_document');
                // Si no hay usuario autenticado, buscar por el documento
                $user = User::where('company_id', $document->company_id)->first();
                if (!$user) {
                    \Log::error('DownloadController.reloadPdf.no_user', [
                        'document_id' => $document->id,
                        'company_id' => $document->company_id
                    ]);
                    return [
                        'success' => false,
                        'message' => 'No se pudo obtener el usuario para regenerar el PDF'
                    ];
                }
                \Log::info('DownloadController.reloadPdf.user_from_document', [
                    'user_id' => $user->id,
                    'company_id' => $user->company_id
                ]);
            } else {
                \Log::info('DownloadController.reloadPdf.auth_user_found', [
                    'user_id' => $user->id
                ]);
            }
            
            $company = $user->company ?? null;
            if(!$company) {
                \Log::warning('DownloadController.reloadPdf.company_null_initial', [
                    'user_id' => $user->id,
                    'document_company_id' => $document->company_id,
                    'identification' => $identification
                ]);
                // Intentar recuperar company por identificación (nit) si viene en el documento
                if($document->identification_number){
                    $company = Company::where('identification_number', $document->identification_number)->first();
                }
                if(!$company && $document->company_id){
                    $company = Company::find($document->company_id);
                }
                if(!$company) {
                    \Log::error('DownloadController.reloadPdf.company_not_found', [
                        'identification' => $identification,
                        'document_id' => $document->id
                    ]);
                    return [
                        'success' => false,
                        'message' => 'No se pudo asociar la compañía del documento'
                    ];
                } else {
                    \Log::info('DownloadController.reloadPdf.company_recovered', [
                        'company_id' => $company->id,
                        'identification_number' => $company->identification_number
                    ]);
                }
            }
            $request = json_decode($document->request_api);
            $typeDocument = TypeDocument::findOrFail($request->type_document_id);

            \Log::info('DownloadController.reloadPdf.type_document_check', [
                'type_document_id' => $request->type_document_id
            ]);

            if(!in_array($request->type_document_id, [1,2,3])){
                \Log::warning('DownloadController.reloadPdf.invalid_document_type');
                return [
                    'success' => false,
                    'message' => 'Tipo de documento no válido'
                ];
            }

            \Log::info('DownloadController.reloadPdf.processing_customer');

            // Customer
            $customerAll = collect($request->customer);
            if(isset($customerAll['municipality_id_fact'])){
                $customerAll['municipality_id'] = Municipality::where('codefacturador', $customerAll['municipality_id_fact'])->first();
            }

            $customer = new User($customerAll->toArray());

            // Customer company - create from array
            $customer->company = new Company($customerAll->toArray());
            
            // Force load relations to trigger withDefault() - access each relation to initialize it
            $customer->company->type_regime;
            $customer->company->type_liability;
            $customer->company->municipality;
            $customer->company->country;
            
            // Log what was created
            \Log::info('DownloadController.reloadPdf.customer_company_created', [
                'has_type_regime' => isset($customer->company->type_regime),
                'type_regime_name' => optional($customer->company->type_regime)->name,
                'has_type_liability' => isset($customer->company->type_liability),
                'type_liability_name' => optional($customer->company->type_liability)->name,
                'has_municipality' => isset($customer->company->municipality),
                'municipality_name' => optional($customer->company->municipality)->name,
                'has_country' => isset($customer->company->country),
                'country_name' => optional($customer->company->country)->name
            ]);

            // Resolution

            if(!isset($company->resolutions)) {
                \Log::warning('DownloadController.reloadPdf.company_resolutions_relation_missing', [
                    'company_id' => $company->id
                ]);
            }
            $count_resolutions = $company->resolutions->where('type_document_id', $request->type_document_id)->count();
            \Log::info('DownloadController.reloadPdf.resolutions_count', [
                'company_id' => $company->id,
                'type_document_id' => $request->type_document_id,
                'count' => $count_resolutions
            ]);

            if($count_resolutions < 2){
                $request->resolution = $company->resolutions->where('type_document_id', $request->type_document_id)->first();
                \Log::info('DownloadController.reloadPdf.resolution_selected_simple', [
                    'resolution_id' => optional($request->resolution)->id,
                    'resolution_number' => optional($request->resolution)->resolution ?? null
                ]);
            }
            else{

                $count_resolutions = $company->resolutions->where('type_document_id', $request->type_document_id)->where('resolution', $request->resolution_number)->count();
                \Log::info('DownloadController.reloadPdf.resolutions_filtered_by_number', [
                    'resolution_number' => $request->resolution_number,
                    'count' => $count_resolutions
                ]);

                if($count_resolutions < 2){
                    $request->resolution = $company->resolutions->where('type_document_id', $request->type_document_id)->where('resolution', $request->resolution_number)->first();
                    \Log::info('DownloadController.reloadPdf.resolution_selected_by_number', [
                        'resolution_id' => optional($request->resolution)->id
                    ]);
                }
                else{
                    $request->resolution = $company->resolutions->where('type_document_id', $request->type_document_id)->where('resolution', $request->resolution_number)->where('prefix', $request->prefix)->first();
                    \Log::info('DownloadController.reloadPdf.resolution_selected_by_number_prefix', [
                        'resolution_id' => optional($request->resolution)->id,
                        'prefix' => $request->prefix
                    ]);
                }

            }

            $request->resolution->number = $request->number;
            $resolution = $request->resolution;
            // Resolution

            $date = $request->date;
            $time = $request->time;

            // dd( $request->payment_form);
            // return json_encode($request->payment_form);

            // Payment form default
            $paymentFormAll = $request->payment_form;
            // $paymentFormAll = (object) array_merge($this->paymentFormDefault, $request->payment_form ?? []);
            $paymentForm = PaymentForm::findOrFail($paymentFormAll->payment_form_id);
            $paymentForm->payment_method_code = PaymentMethod::findOrFail($paymentFormAll->payment_method_id)->code;
            $paymentForm->nameMethod = PaymentMethod::findOrFail($paymentFormAll->payment_method_id)->name;
            $paymentForm->payment_due_date = $paymentFormAll->payment_due_date ?? null;
            $paymentForm->duration_measure = $paymentFormAll->duration_measure ?? null;
            // Payment form default

            // Retenciones globales
            $withHoldingTaxTotal = collect();

            // return $request->with_holding_tax_total;
            $new_request = request()->merge(json_decode($document->request_api, true));

            // Aplicar override de formato si está presente
            $formatOverride = config('pdf.page_format_override');
            if ($formatOverride) {
                if ($formatOverride === 'ticket') {
                    // Para ticket/tirilla usar template especial
                    $new_request = $new_request->merge([
                        'invoice_template' => '1',
                        'is_tirilla2' => true,
                        'template_token' => bin2hex(random_bytes(16))
                    ]);
                    \Log::debug('DownloadController.request_override_ticket', [
                        'format' => $formatOverride,
                        'invoice_template' => '1',
                        'is_tirilla2' => true
                    ]);
                } elseif (in_array($formatOverride, ['letter', 'a4'], true)) {
                    // Para letter y a4 usar template normal
                    $new_request = $new_request->merge([
                        'invoice_template' => '2',
                        'is_tirilla2' => false,
                        'template_token' => bin2hex(random_bytes(16))
                    ]);
                    \Log::debug('DownloadController.request_override', [
                        'format' => $formatOverride,
                        'invoice_template' => '2',
                        'is_tirilla2' => false
                    ]);
                }
            }

            foreach($new_request->with_holding_tax_total ?? [] as $item) {
                $withHoldingTaxTotal->push(new TaxTotal($item));
            }
            // Retenciones globales

            // Notes
            $notes = $request->notes ?? null;

            // $request->legal_monetary_totals = json_decode(json_encode($request->legal_monetary_totals), true);
            // $request->tax_totals = json_decode(json_encode($request->tax_totals), true);
            // $request->customer = json_decode(json_encode($request->customer), true);
            // $request->invoice_lines = json_decode(json_encode($request->invoice_lines), true);

            // $new_request = new InvoiceRequest(json_decode($document->request_api, true));

            // Ultimo parametro en NULL corresponde a los campos del sector salud, si se desean incluir se tendran que enviar.
            \Log::info('DownloadController.reloadPdf.calling_createPDF');
            
            // Convert paymentForm to Collection for template compatibility (needs count() and array access)
            $paymentFormArray = collect([$paymentForm]);
            
            // Health fields for sector salud
            if(isset($request->health_fields)){
                try {
                    // Convert object to array recursively for HealthField constructor
                    $healthFieldsArray = json_decode(json_encode($request->health_fields), true);
                    $healthfields = new HealthField($healthFieldsArray);
                    \Log::info('DownloadController.reloadPdf.health_fields_found', [
                        'has_users_info' => isset($healthfields->users_info),
                        'users_count' => isset($healthfields->users_info) ? count($healthfields->users_info) : 0,
                        'print_to_pdf' => $healthfields->print_users_info_to_pdf ?? false
                    ]);
                } catch (\Exception $e) {
                    \Log::error('DownloadController.reloadPdf.health_fields_error', [
                        'message' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => basename($e->getFile())
                    ]);
                    $healthfields = NULL;
                }
            }
            else{
                $healthfields = NULL;
                \Log::info('DownloadController.reloadPdf.health_fields_not_found');
            }
            
            $this->createPDF($user, $company, $customer, $typeDocument, $resolution, $date, $time, $paymentFormArray, $new_request, $cufe, "INVOICE", $withHoldingTaxTotal, $notes, $healthfields);

            \Log::info('DownloadController.reloadPdf.PDF_created_reading_file');
            
            // Leer el archivo PDF generado
            $pdfPath = storage_path("app/public/{$identification}/{$file}");
            
            if (!file_exists($pdfPath)) {
                \Log::error('DownloadController.reloadPdf.file_not_found_after_creation', [
                    'path' => $pdfPath
                ]);
                return [
                    'success' => false,
                    'message' => 'El PDF fue generado pero no se encontró en el almacenamiento'
                ];
            }
            
            $pdfContent = file_get_contents($pdfPath);
            $base64Content = base64_encode($pdfContent);
            
            \Log::info('DownloadController.reloadPdf.SUCCESS', [
                'file_size' => strlen($pdfContent),
                'base64_size' => strlen($base64Content)
            ]);
            
            return [
                'success' => true,
                'message' => 'PDF regenerado correctamente',
                'filebase64' => $base64Content
            ];

        }
        catch(Exception $e) {

            \Log::error('DownloadController.reloadPdf.EXCEPTION', [
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => "{$e->getLine()} - {$e->getMessage()}"
            ];

        }
        finally {
            // Limpiar override de formato
            config(['pdf.page_format_override' => null]);
        }

    }

    /**
     * Descarga pública de archivos
     *
     * @param $identification
     * @param $file
     * @param $type_response
    */
    public function publicDownload($identification, $file, $type_response = false)
    {

//        if(!config('system_configuration.allow_public_download')){
          if(!env('ALLOW_PUBLIC_DONWLOAD', false)){
                $u = new \App\Utils;

            if(strpos($file, 'Attachment-') === false and strpos($file, 'ZipAttachm-') === false){

                if(file_exists(storage_path("app/public/{$identification}/{$file}")))
                    if($type_response && $type_response === 'BASE64')
                        return [
                            'success' => true,
                            'message' => "Archivo: ".$file." se encontro.",
                            'filebase64'=>base64_encode(file_get_contents(storage_path("app/public/{$identification}/{$file}")))
                        ];
                    else
                        return Storage::download("public/{$identification}/{$file}");
                else
                    return [
                        'success' => false,
                        'message' => "No se encontro el archivo: ".$file
                    ];
            }
            else{
                if(strpos($file, 'ZipAttachm-') === false){
                    $filename = $u->attacheddocumentname($identification, $file);
                    if(file_exists(storage_path("app/public/{$identification}/{$filename}.xml")))
                        if($type_response && $type_response === 'BASE64')
                            return [
                                'success' => true,
                                'message' => "Archivo: ".$filename.".xml se encontro.",
                                'filebase64'=>base64_encode(file_get_contents(storage_path("app/public/{$identification}/{$filename}.xml")))
                            ];
                        else
                            return Storage::download("public/{$identification}/{$filename}.xml");
                    else
                        return [
                            'success' => false,
                            'message' => "No se encontro el archivo: ".$filename.".xml"
                        ];
                }
                else{
                    $filename = $u->attacheddocumentname($identification, $file);
                    if(file_exists(storage_path("app/public/{$identification}/{$filename}.zip")))
                        if($type_response && $type_response === 'BASE64')
                            return [
                                'success' => true,
                                'message' => "Archivo: ".$filename.".zip se encontro.",
                                'filebase64'=>base64_encode(file_get_contents(storage_path("app/public/{$identification}/{$filename}.zip")))
                            ];
                        else
                            return Storage::download("public/{$identification}/{$filename}.zip");
                    else
                        return [
                            'success' => false,
                            'message' => "No se encontro el archivo: ".$filename.".zip"
                        ];
                }
            }
        }else{
            return [
                'success' => false,
                'message' => 'La descarga pública de archivos se encuentra habilitada (API)'
            ];
        }
    }

    /**
     * Maneja la descarga de archivos con opción de regenerar PDFs en diferentes formatos
     */
    public function download($identification, $file, $type_response = false)
    {
        // Log de entrada para debugging
        \Log::info('DownloadController.download.called', [
            'identification' => $identification,
            'file' => $file,
            'format_param' => request()->query('format'),
            'all_params' => request()->all(),
            'query_string' => request()->getQueryString()
        ]);

        // Si es un PDF y se especifica formato, regenerar
        $format = request()->query('format');
        $isPdf = pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
        
        \Log::info('DownloadController.download.check', [
            'isPdf' => $isPdf,
            'format' => $format,
            'shouldRegenerate' => ($isPdf && $format && in_array(strtolower($format), ['letter', 'a4'], true))
        ]);
        
        if ($isPdf && $format && in_array(strtolower($format), ['letter', 'a4'], true)) {
            // Buscar el documento para obtener el CUFE
            $document = Document::where([
                ['identification_number', $identification],
                ['pdf', $file]
            ])->first();
            
            if ($document) {
                \Log::info('DownloadController.download.regenerating', [
                    'cufe' => $document->cufe,
                    'format' => $format
                ]);
                // Regenerar PDF con el formato especificado
                return $this->reloadPdf($identification, $file, $document->cufe);
            } else {
                \Log::warning('DownloadController.download.document_not_found', [
                    'identification' => $identification,
                    'file' => $file
                ]);
            }
        }
        
        // Descarga normal del archivo existente
        $u = new \App\Utils;
        if(strpos($file, 'Attachment-') === false and strpos($file, 'ZipAttachm-') === false) {
            if(file_exists(storage_path("app/public/{$identification}/{$file}"))) {
                if($type_response && $type_response === 'BASE64')
                    return [
                        'success' => true,
                        'message' => "Archivo: ".$file." se encontro.",
                        'filebase64'=>base64_encode(file_get_contents(storage_path("app/public/{$identification}/{$file}")))
                    ];
                else
                    return Storage::download("public/{$identification}/{$file}");
            }
            else
                return [
                    'success' => false,
                    'message' => "No se encontro el archivo: ".$file
                ];
        }
        else {
            if(strpos($file, 'ZipAttachm-') === false) {
                $filename = $u->attacheddocumentname($identification, $file);
                if(file_exists(storage_path("app/public/{$identification}/{$filename}.xml"))) {
                    if($type_response && $type_response === 'BASE64')
                        return [
                            'success' => true,
                            'message' => "Archivo: ".$filename.".xml se encontro.",
                            'filebase64'=>base64_encode(file_get_contents(storage_path("app/public/{$identification}/{$filename}.xml")))
                        ];
                    else
                        return Storage::download("public/{$identification}/{$filename}.xml");
                }
                else
                    return [
                        'success' => false,
                        'message' => "No se encontro el archivo: ".$filename.".xml"
                    ];
            }
            else {
                $filename = $u->attacheddocumentname($identification, $file);
                if(file_exists(storage_path("app/public/{$identification}/{$filename}.zip"))) {
                    if($type_response && $type_response === 'BASE64')
                        return [
                            'success' => true,
                            'message' => "Archivo: ".$filename.".zip se encontro.",
                            'filebase64'=>base64_encode(file_get_contents(storage_path("app/public/{$identification}/{$filename}.zip")))
                        ];
                    else
                        return Storage::download("public/{$identification}/{$filename}.zip");
                }
                else
                    return [
                        'success' => false,
                        'message' => "No se encontro el archivo: ".$filename.".zip"
                    ];
            }
        }
    }
}

