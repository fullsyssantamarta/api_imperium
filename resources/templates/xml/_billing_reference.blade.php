<?php
    if(isset($billingReference)) {
        $billingReferencePrefix = data_get($billingReference, 'prefix');

        if(!$billingReferencePrefix) {
            $billingReferencePrefix = data_get($request, 'billing_reference.prefix');
        }

        if(!$billingReferencePrefix && isset($resolution)) {
            $billingReferencePrefix = data_get($resolution, 'prefix');
        }

        if(!$billingReferencePrefix) {
            $billingReferencePrefix = data_get($request, 'resolution.prefix');
        }

        if(!$billingReferencePrefix) {
            $billingReferencePrefix = data_get($request, 'prefix');
        }

        $billingReferenceNumber = data_get($billingReference, 'number', '');
        $billingReferenceNumber = is_string($billingReferenceNumber)
            ? trim($billingReferenceNumber)
            : trim((string) $billingReferenceNumber);
        $billingReferencePrefix = $billingReferencePrefix ? trim($billingReferencePrefix) : null;

        if($billingReferencePrefix) {
            $normalizedNumber = ltrim($billingReferenceNumber);

            if(strpos($normalizedNumber, $billingReferencePrefix) === 0) {
                $normalizedNumber = substr($normalizedNumber, strlen($billingReferencePrefix));
            }

            $normalizedNumber = ltrim($normalizedNumber, '-');

            $billingReferenceNumber = $billingReferencePrefix . $normalizedNumber;
        }

        $billingReference->display_number = $billingReferenceNumber;
    }
?>
<cac:BillingReference>
    <cac:InvoiceDocumentReference>
        <cbc:ID>{{preg_replace("/[\r\n|\n|\r]+/", "", $billingReference->display_number ?? $billingReference->number)}}</cbc:ID>
        @if($typeDocument->id == '11' || $typeDocument->id == '13')
            <cbc:UUID schemeName="CUDS-SHA384">{{preg_replace("/[\r\n|\n|\r]+/", "", $billingReference->uuid)}}</cbc:UUID>
        @else
            <cbc:UUID schemeName="{{preg_replace("/[\r\n|\n|\r]+/", "", $billingReference->scheme_name)}}">{{preg_replace("/[\r\n|\n|\r]+/", "", $billingReference->uuid)}}</cbc:UUID>
        @endif
        <cbc:IssueDate>{{preg_replace("/[\r\n|\n|\r]+/", "", $billingReference->issue_date)}}</cbc:IssueDate>
        @if($typeDocument->id == '26')
            <cbc:DocumentTypeCode>{{preg_replace("/[\r\n|\n|\r]+/", "", $billingReference->document_type_code)}}</cbc:DocumentTypeCode>
        @endif
    </cac:InvoiceDocumentReference>
</cac:BillingReference>
