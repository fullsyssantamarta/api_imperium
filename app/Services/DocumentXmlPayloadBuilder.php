<?php

namespace App\Services;

use App\Document;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class DocumentXmlPayloadBuilder
{
    public function build(Document $document, array $fallback = []): ?array
    {
        $path = $this->resolveXmlPath($document);

        if (!$path || !is_file($path)) {
            Log::warning('document.xml_payload.missing_path', [
                'document_id' => $document->id,
                'stored_xml' => $document->xml,
            ]);

            return $fallback ?: null;
        }

        try {
            $xml = new SimpleXMLElement(file_get_contents($path));
        } catch (\Throwable $exception) {
            Log::error('document.xml_payload.invalid_xml', [
                'document_id' => $document->id,
                'xml_path' => $path,
                'exception' => $exception->getMessage(),
            ]);

            return $fallback ?: null;
        }

        $namespaces = $xml->getNamespaces(true);
        if (isset($namespaces['cbc'])) {
            $xml->registerXPathNamespace('cbc', $namespaces['cbc']);
        }
        if (isset($namespaces['cac'])) {
            $xml->registerXPathNamespace('cac', $namespaces['cac']);
        }

        $payload = $fallback;
    $payload['type_document_id'] = $document->type_document_id;
        $payload['prefix'] = $document->prefix;
        $payload['number'] = $this->extractString($xml, '//cbc:ID') ?: $document->number;
        $payload['cufe'] = $this->extractString($xml, '//cbc:UUID') ?: $document->cufe;
        $payload['date'] = $this->extractString($xml, '//cbc:IssueDate') ?: Arr::get($fallback, 'date');
        $payload['time'] = $this->extractString($xml, '//cbc:IssueTime') ?: Arr::get($fallback, 'time');
    $payload['page_format'] = config('pdf.page_format_override') ?: Arr::get($fallback, 'page_format');

        $payload['payment_form'] = $this->resolvePaymentForms(Arr::get($fallback, 'payment_form', []), $this->extractPaymentForms($xml));
        $payload['tax_totals'] = $this->mergeTaxTotals(Arr::get($fallback, 'tax_totals', []), $this->extractTaxTotals($xml));
        $payload['allowance_charges'] = $this->resolveAllowanceCharges(Arr::get($fallback, 'allowance_charges', []), $this->extractAllowanceCharges($xml));
        $payload['legal_monetary_totals'] = $this->mergeLegalMonetaryTotals(Arr::get($fallback, 'legal_monetary_totals', []), $this->extractLegalMonetaryTotals($xml));
        $payload['invoice_lines'] = $this->mergeInvoiceLines(Arr::get($fallback, 'invoice_lines', []), $this->extractInvoiceLines($xml));

        return $payload;
    }

    protected function resolveXmlPath(Document $document): ?string
    {
        $candidates = [];

        if ($document->xml) {
            $clean = ltrim($document->xml, '/');
            $candidates[] = storage_path('app/' . $clean);
            $candidates[] = storage_path('app/public/' . $clean);
            $candidates[] = storage_path('app/' . $document->xml);
            $candidates[] = storage_path('app/public/' . $document->xml);

            if ($document->identification_number) {
                $candidates[] = storage_path('app/public/' . $document->identification_number . '/' . $clean);
                $candidates[] = storage_path('app/' . $document->identification_number . '/' . $clean);
            }
        }

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return realpath($candidate) ?: $candidate;
            }
        }

        return null;
    }

    protected function extractString(SimpleXMLElement $xml, string $xpath): ?string
    {
        $nodes = $xml->xpath($xpath);

        if (!$nodes || !isset($nodes[0])) {
            return null;
        }

        $value = trim((string) $nodes[0]);

        return $value !== '' ? $value : null;
    }

    protected function extractPaymentForms(SimpleXMLElement $xml): array
    {
        $results = [];
        $nodes = $xml->xpath('//cac:PaymentMeans');

        foreach ($nodes as $node) {
            $cbc = $node->children($node->getNamespaces()['cbc'] ?? null);
            $results[] = [
                'payment_form_id' => (int) ($cbc->PaymentMeansCode ?? 0),
                'payment_method_id' => (int) ($cbc->PaymentMeansCode ?? 0),
                'payment_due_date' => isset($cbc->PaymentDueDate) ? (string) $cbc->PaymentDueDate : null,
                'duration_measure' => isset($cbc->PaymentDueDate) ? null : null,
                'nameMethod' => isset($cbc->PaymentMeansCode) ? (string) $cbc->PaymentMeansCode : null,
            ];
        }

        return array_filter($results);
    }

    protected function extractTaxTotals(SimpleXMLElement $xml): array
    {
        $results = [];
        $nodes = $xml->xpath('//cac:TaxTotal');

        foreach ($nodes as $node) {
            $cbc = $node->children($node->getNamespaces()['cbc'] ?? null);
            $amount = isset($cbc->TaxAmount) ? (float) $cbc->TaxAmount : 0.0;
            $currency = isset($cbc->TaxAmount) ? (string) $cbc->TaxAmount['currencyID'] : null;

            $subtotals = [];
            $taxSubtotals = $node->xpath('cac:TaxSubtotal');
            foreach ($taxSubtotals as $subtotal) {
                $subtotalNamespaces = $subtotal->getNamespaces();
                $cbcSubtotal = $subtotal->children($subtotalNamespaces['cbc'] ?? null);
                $cacSubtotal = $subtotal->children($subtotalNamespaces['cac'] ?? null);

                $scheme = $cacSubtotal->TaxCategory->TaxScheme ?? null;
                $schemeId = $scheme ? (string) $scheme->children($scheme->getNamespaces()['cbc'] ?? null)->ID : null;

                $subtotals[] = [
                    'taxable_amount' => isset($cbcSubtotal->TaxableAmount) ? (float) $cbcSubtotal->TaxableAmount : 0.0,
                    'tax_amount' => isset($cbcSubtotal->TaxAmount) ? (float) $cbcSubtotal->TaxAmount : 0.0,
                    'percent' => isset($cbcSubtotal->Percent) ? (float) $cbcSubtotal->Percent : null,
                    'tax_scheme_id' => $schemeId,
                    'currency' => isset($cbcSubtotal->TaxAmount) ? (string) $cbcSubtotal->TaxAmount['currencyID'] : $currency,
                ];
            }

            $results[] = [
                'tax_amount' => $amount,
                'currency' => $currency,
                'tax_subtotals' => $subtotals,
            ];
        }

        return $results;
    }

    protected function extractAllowanceCharges(SimpleXMLElement $xml): array
    {
        $results = [];
        $nodes = $xml->xpath('//cac:AllowanceCharge');

        foreach ($nodes as $node) {
            $namespaces = $node->getNamespaces();
            $cbc = $node->children($namespaces['cbc'] ?? null);

            $results[] = [
                'charge_indicator' => isset($cbc->ChargeIndicator) ? filter_var((string) $cbc->ChargeIndicator, FILTER_VALIDATE_BOOLEAN) : false,
                'amount' => isset($cbc->Amount) ? (float) $cbc->Amount : 0.0,
                'base_amount' => isset($cbc->BaseAmount) ? (float) $cbc->BaseAmount : null,
                'reason_code' => isset($cbc->AllowanceChargeReasonCode) ? (string) $cbc->AllowanceChargeReasonCode : null,
                'reason' => isset($cbc->AllowanceChargeReason) ? (string) $cbc->AllowanceChargeReason : null,
            ];
        }

        return $results;
    }

    protected function extractLegalMonetaryTotals(SimpleXMLElement $xml): array
    {
        $node = $xml->xpath('//cac:LegalMonetaryTotal');
        if (!$node || !isset($node[0])) {
            return [];
        }

        $namespaces = $node[0]->getNamespaces();
        $cbc = $node[0]->children($namespaces['cbc'] ?? null);

        return [
            'line_extension_amount' => isset($cbc->LineExtensionAmount) ? (float) $cbc->LineExtensionAmount : 0.0,
            'tax_exclusive_amount' => isset($cbc->TaxExclusiveAmount) ? (float) $cbc->TaxExclusiveAmount : 0.0,
            'tax_inclusive_amount' => isset($cbc->TaxInclusiveAmount) ? (float) $cbc->TaxInclusiveAmount : 0.0,
            'allowance_total_amount' => isset($cbc->AllowanceTotalAmount) ? (float) $cbc->AllowanceTotalAmount : 0.0,
            'payable_amount' => isset($cbc->PayableAmount) ? (float) $cbc->PayableAmount : 0.0,
        ];
    }

    protected function extractInvoiceLines(SimpleXMLElement $xml): array
    {
        $results = [];
        $nodes = $xml->xpath('//cac:InvoiceLine');

        foreach ($nodes as $node) {
            $namespaces = $node->getNamespaces();
            $cbc = $node->children($namespaces['cbc'] ?? null);
            $cac = $node->children($namespaces['cac'] ?? null);

            $description = null;
            if (isset($cac->Item)) {
                $itemNamespaces = $cac->Item->getNamespaces();
                $cbcItem = $cac->Item->children($itemNamespaces['cbc'] ?? null);
                $description = isset($cbcItem->Description) ? (string) $cbcItem->Description : null;
            }

            $priceAmount = null;
            if (isset($cac->Price)) {
                $priceNamespaces = $cac->Price->getNamespaces();
                $cbcPrice = $cac->Price->children($priceNamespaces['cbc'] ?? null);
                $priceAmount = isset($cbcPrice->PriceAmount) ? (float) $cbcPrice->PriceAmount : null;
            }

            $results[] = [
                'description' => $description,
                'invoiced_quantity' => isset($cbc->InvoicedQuantity) ? (float) $cbc->InvoicedQuantity : 0.0,
                'line_extension_amount' => isset($cbc->LineExtensionAmount) ? (float) $cbc->LineExtensionAmount : 0.0,
                'price_amount' => $priceAmount,
            ];
        }

        return $results;
    }

    protected function resolvePaymentForms(array $fallback, array $extracted): array
    {
        if (!empty($extracted)) {
            return $extracted;
        }

        return $fallback;
    }

    protected function resolveAllowanceCharges(array $fallback, array $extracted): array
    {
        if (!empty($extracted)) {
            return $extracted;
        }

        return $fallback;
    }

    protected function mergeLegalMonetaryTotals(array $fallback, array $extracted): array
    {
        if (empty($fallback)) {
            return $extracted;
        }

        foreach ($extracted as $key => $value) {
            if ($value !== null) {
                $fallback[$key] = $value;
            }
        }

        return $fallback;
    }

    protected function mergeTaxTotals(array $fallback, array $extracted): array
    {
        if (empty($fallback)) {
            return $extracted;
        }

        if (empty($extracted)) {
            return $fallback;
        }

        foreach ($fallback as $index => $tax) {
            if (!isset($extracted[$index])) {
                continue;
            }

            $fallback[$index]['tax_amount'] = $extracted[$index]['tax_amount'] ?? Arr::get($fallback[$index], 'tax_amount');
            if (isset($extracted[$index]['tax_subtotals'])) {
                $fallback[$index]['tax_subtotals'] = $extracted[$index]['tax_subtotals'];
            }
        }

        return $fallback;
    }

    protected function mergeInvoiceLines(array $fallback, array $extracted): array
    {
        if (empty($fallback)) {
            return $extracted;
        }

        if (empty($extracted)) {
            return $fallback;
        }

        foreach ($fallback as $index => $line) {
            if (!isset($extracted[$index])) {
                continue;
            }

            foreach (['invoiced_quantity', 'line_extension_amount', 'price_amount', 'description'] as $field) {
                if (isset($extracted[$index][$field]) && $extracted[$index][$field] !== null) {
                    $fallback[$index][$field] = $extracted[$index][$field];
                }
            }
        }

        return $fallback;
    }
}
