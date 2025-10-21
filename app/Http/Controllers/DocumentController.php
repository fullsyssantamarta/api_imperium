<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;
use App\Http\Resources\DocumentCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\RegeneratePDFController;
use App\Http\Controllers\Api\DownloadController;

class DocumentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
       // $list =  new CompaniesCollection(User::all());
        //return json_encode($list);
        return view('documents.index') ;
    }



    public function records(Request $request)
    {
        $records = Document::all();
        return new DocumentCollection($records);
    }


    public function downloadxml($xml)
    {
        //$invoice =  Document::find($id);
        return response()->download(storage_path($xml));
    }

    public function downloadpdf(Request $request, $pdf)
    {
        $format = $this->normalizeFormat($request->query('format'));

        \Log::info('DocumentController.downloadpdf.start', [
            'pdf' => $pdf,
            'format_param' => $request->query('format'),
            'normalized_format' => $format
        ]);

        $document = Document::where('pdf', $pdf)
            ->orWhere('pdf', ltrim($pdf, '/'))
            ->orWhere('pdf', "public/" . ltrim($pdf, '/'))
            ->first();

        if (!$document) {
            \Log::warning('DocumentController.downloadpdf.document_not_found', ['pdf' => $pdf]);
            return $this->downloadFallback($pdf);
        }

        \Log::info('DocumentController.downloadpdf.document_found', [
            'document_id' => $document->id,
            'prefix' => $document->prefix,
            'number' => $document->number,
            'cufe' => $document->cufe
        ]);

        if (!$format) {
            \Log::info('DocumentController.downloadpdf.no_format_fallback');
            return $this->downloadFromDocument($document);
        }

        $cufe = $document->cufe ?? $document->cude ?? $document->cune ?? null;

        if (!$document->prefix || !$document->number || !$cufe) {
            \Log::warning('DocumentController.downloadpdf.missing_data', [
                'has_prefix' => !empty($document->prefix),
                'has_number' => !empty($document->number),
                'has_cufe' => !empty($cufe)
            ]);
            return response()->download(storage_path("app/{$pdf}"));
        }

        config(['pdf.page_format_override' => $format]);
        \Log::info('DocumentController.downloadpdf.calling_regenerator', ['format' => $format]);

        try {
            // Usar DownloadController que tiene toda la lógica de regeneración
            $downloadController = app(DownloadController::class);
            $result = $downloadController->reloadPdf(
                $document->identification_number,
                $pdf,
                $cufe
            );
            
            \Log::info('DocumentController.downloadpdf.regenerator_result', [
                'success' => $result['success'] ?? null,
                'has_message' => !empty($result['message'] ?? null)
            ]);
            
            // Si reloadPdf fue exitoso, descargar el PDF regenerado
            if (is_array($result) && ($result['success'] ?? false)) {
                return response()->download(storage_path("app/public/{$document->identification_number}/{$pdf}"));
            }
        } catch (\Throwable $throwable) {
            Log::error('Error regenerating PDF with custom format', [
                'document_id' => $document->id,
                'format' => $format,
                'exception' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString()
            ]);
            $result = null;
        } finally {
            config(['pdf.page_format_override' => null]);
        }

        if (is_array($result) && ($result['success'] ?? false) && !empty($result['filebase64'])) {
            $contents = base64_decode($result['filebase64']);

            if ($contents !== false) {
                $baseName = basename($pdf);
                $originalName = pathinfo($baseName, PATHINFO_FILENAME) ?: $baseName;
                $extension = pathinfo($baseName, PATHINFO_EXTENSION) ?: 'pdf';
                $suffix = strtoupper($format);
                $downloadName = trim($originalName . '-' . $suffix, '-');

                return response($contents, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $downloadName . '.' . $extension . '"',
                ]);
            }
        }

        return $this->downloadFromDocument($document);
    }

    protected function normalizeFormat($format)
    {
        if (!is_string($format)) {
            return null;
        }

        $value = strtolower(trim($format));

        if (in_array($value, ['letter', 'carta'], true)) {
            return 'letter';
        }

        if ($value === 'a4') {
            return 'a4';
        }

        return null;
    }

    protected function downloadFromDocument(Document $document)
    {
        $candidates = [
            storage_path("app/public/{$document->identification_number}/{$document->pdf}"),
            storage_path("app/{$document->pdf}"),
            storage_path("app/{$document->identification_number}/{$document->pdf}")
        ];

        foreach ($candidates as $path) {
            if ($path && file_exists($path)) {
                return response()->download($path);
            }
        }

        abort(404, 'Archivo PDF no encontrado');
    }

    protected function downloadFallback(string $pdf)
    {
        $candidates = [
            storage_path("app/{$pdf}"),
            storage_path("app/public/{$pdf}"),
            storage_path("app/" . ltrim($pdf, '/')),
        ];

        foreach ($candidates as $path) {
            if ($path && file_exists($path)) {
                return response()->download($path);
            }
        }

        abort(404, 'Archivo PDF no encontrado');
    }

    public function changeState(Request $request)
    {
        $document = Document::findOrFail($request->document_id);

        if($document) {
            $document->state_document_id = 1;
            $document->save();
        }

        return redirect()->back();
    }

}
