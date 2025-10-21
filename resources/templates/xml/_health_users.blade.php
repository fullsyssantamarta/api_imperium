@foreach ($healthUsers as $key => $healthUser)
    @php
        // Safe lookups for catalog data
        $docType = \App\HealthTypeDocumentIdentification::find($healthUser->health_type_document_identification_id);
        $typeUser = \App\HealthTypeUser::find($healthUser->health_type_user_id);
        $paymentMethod = \App\HealthContractingPaymentMethod::find($healthUser->health_contracting_payment_method_id);
        $coverage = \App\HealthCoverage::find($healthUser->health_coverage_id);
    @endphp
    <Collection schemeName="Usuario">
        <AdditionalInformation>
            <Name>CODIGO_PRESTADOR</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->provider_code ?? '')}}</Value>
        </AdditionalInformation>

        @if($docType)
        <AdditionalInformation>
            <Name>TIPO_DOCUMENTO_USUARIO</Name>
            <Value schemeName="salud_tipo_documento.gc" schemeID="{{preg_replace("/[\r\n|\n|\r]+/", "", $docType->code)}}">{{preg_replace("/[\r\n|\n|\r]+/", "", $docType->name)}}</Value>
        </AdditionalInformation>
        @endif

        <AdditionalInformation>
            <Name>NUMERO_DOCUMENTO_USUARIO</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->identification_number ?? '')}}</Value>
        </AdditionalInformation>

        <AdditionalInformation>
            <Name>PRIMER_APELLIDO</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->surname ?? '')}}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
            <Name>SEGUNDO_APELLIDO</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->second_surname ?? '')}}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
            <Name>PRIMER_NOMBRE</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->first_name ?? '')}}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
            <Name>SEGUNDO_NOMBRE</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->middle_name ?? '')}}</Value>
        </AdditionalInformation>

        @if($typeUser)
        <AdditionalInformation>
            <Name>TIPO_USUARIO</Name>
            <Value schemeName="salud_tipo_usuario.gc" schemeID="{{preg_replace("/[\r\n|\n|\r]+/", "", $typeUser->code)}}">{{preg_replace("/[\r\n|\n|\r]+/", "", $typeUser->name)}}</Value>
        </AdditionalInformation>
        @endif

        @if($paymentMethod)
        <AdditionalInformation>
            <Name>MODALIDAD_PAGO</Name>
            <Value schemeName="salud_modalidad_pago.gc" schemeID="{{preg_replace("/[\r\n|\n|\r]+/", "", $paymentMethod->code)}}">{{preg_replace("/[\r\n|\n|\r]+/", "", $paymentMethod->name)}}</Value>
        </AdditionalInformation>
        @endif
        @if($coverage)
        <AdditionalInformation>
            <Name>COBERTURA_PLAN_BENEFICIOS</Name>
            <Value schemeName="salud_cobertura.gc" schemeID="{{preg_replace("/[\r\n|\n|\r]+/", "", $coverage->code)}}">{{preg_replace("/[\r\n|\n|\r]+/", "", $coverage->name)}}</Value>
        </AdditionalInformation>
        @endif
        <AdditionalInformation>
            <Name>NUMERO_CONTRATO</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->contract_number)}}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
            <Name>NUMERO_POLIZA</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->policy_number)}}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
            <Name>COPAGO</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->co_payment)}}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
            <Name>CUOTA_MODERADORA</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->moderating_fee)}}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
            <Name>PAGOS_COMPARTIDOS</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->shared_payment)}}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
            <Name>ANTICIPO</Name>
            <Value>{{preg_replace("/[\r\n|\n|\r]+/", "", $healthUser->advance_payment)}}</Value>
        </AdditionalInformation>
    </Collection>
@endforeach
