# Documentación Swagger APIDIAN - Sector Salud y RIPS

## Resumen de Endpoints Documentados

### SECTOR SALUD (Section 11)

#### Factura Electrónica Sector Salud

**Endpoint Principal:** `POST /api/ubl2.1/invoice`

**Descripción:** Envío de factura electrónica para el sector salud con información de pacientes y servicios médicos.

**Campo Especial: `health_fields`**
```json
{
  "health_fields": {
    "invoice_period_start_date": "2025-01-01",
    "invoice_period_end_date": "2025-01-29",
    "health_type_operation_id": 1,
    "print_users_info_to_pdf": true,
    "users_info": [
      {
        "provider_code": "AF-0000500-85-XX-001",
        "health_type_document_identification_id": 1,
        "identification_number": "A89008003",
        "surname": "OBANDO",
        "second_surname": "LONDOÑO",
        "first_name": "ALEXANDER",
        "middle_name": "ANDRES",
        "health_type_user_id": 1,
        "health_contracting_payment_method_id": 2,
        "health_coverage_id": 5,
        "autorization_numbers": "A12345;604567;AX-2345",
        "mipres": "RNA3D345;664FF04567;ARXXX-2765345",
        "mipres_delivery": "RN6645G-345;6-064XX54FF04567;XXX-2-OO-987D65345",
        "contract_number": "1000-2021-0005698",
        "policy_number": "1045-2FG01-0567228",
        "co_payment": "3300.00",
        "moderating_fee": "5800.00",
        "recovery_fee": "105000.00",
        "shared_payment": "225000.00"
      }
    ]
  }
}
```

**Campos del objeto `health_fields`:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `invoice_period_start_date` | string (date) | Sí | Fecha de inicio del período de facturación |
| `invoice_period_end_date` | string (date) | Sí | Fecha de fin del período de facturación |
| `health_type_operation_id` | integer | Sí | ID del tipo de operación de salud |
| `print_users_info_to_pdf` | boolean | No | Indica si se debe imprimir información de usuarios en PDF |
| `users_info` | array | Sí | Array de información de pacientes |

**Campos del objeto `users_info` (paciente):**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `provider_code` | string | Sí | Código del prestador de servicios de salud |
| `health_type_document_identification_id` | integer | Sí | ID del tipo de documento de identificación |
| `identification_number` | string | Sí | Número de identificación del paciente |
| `surname` | string | Sí | Primer apellido |
| `second_surname` | string | No | Segundo apellido |
| `first_name` | string | Sí | Primer nombre |
| `middle_name` | string | No | Segundo nombre |
| `health_type_user_id` | integer | Sí | ID del tipo de usuario de salud |
| `health_contracting_payment_method_id` | integer | Sí | ID del método de pago de contratación |
| `health_coverage_id` | integer | Sí | ID de la cobertura de salud (EPS) |
| `autorization_numbers` | string | No | Números de autorización separados por punto y coma (;) |
| `mipres` | string | No | Prescripciones MIPRES separadas por punto y coma (;) |
| `mipres_delivery` | string | No | Entregas MIPRES separadas por punto y coma (;) |
| `contract_number` | string | No | Número de contrato |
| `policy_number` | string | No | Número de póliza |
| `co_payment` | string (decimal) | No | Valor de copago |
| `moderating_fee` | string (decimal) | No | Cuota moderadora |
| `recovery_fee` | string (decimal) | No | Cuota de recuperación |
| `shared_payment` | string (decimal) | No | Pago compartido |

**Variaciones de Factura Sector Salud:**

1. **SendInvoice-TestSetId Health** - Factura única de salud
2. **Health Multiples pacientes** - Factura con múltiples pacientes
3. **Capita Inicial** - Sin atención de pacientes, tipo SS-RECAUDO, modalidad inicio de contrato por cápita
4. **Capita Periodo** - Con atención de pacientes, tipo SS-CUFE

### API RIPS (Section 15)

**Base URL:** `{{url_apirips}}`

#### 1. Estado del Servicio

**GET /BasicHealth**

**Descripción:** Muestra los datos de la versión actual del apirips-docker

**Autenticación:** No requiere

**Respuesta:**
```
ApplicationName: FVE.WebAPILocal
Version: 5.4.1.0
EnvironmentName: DockerStage
DateTime: 2025-05-19 02:42:19.304 PM
TimeZone: Coordinated Universal Time
```

#### 2. Autenticación

**POST /api/auth/LoginSISPRO**

**Descripción:** Login para acceso al sistema SISPRO con credenciales de sector salud

**Autenticación:** No requiere

**Request Body:**
```json
{
  "persona": {
    "identificacion": {
      "tipo": "CC",
      "numero": "16602426"
    }
  },
  "clave": "Cabl20000",
  "nit": "901355357"
}
```

**Campos:**
- `persona.identificacion.tipo`: Tipo de documento (CC, TI, CE, etc.)
- `persona.identificacion.numero`: Número de documento
- `clave`: Contraseña del usuario
- `nit`: NIT de la empresa prestadora de servicios

**Respuesta Exitosa (200):**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "login": true,
  "registrado": true,
  "errors": null
}
```

**Respuesta Error (200):**
```json
{
  "token": null,
  "login": false,
  "registrado": false,
  "errors": [
    "Sus Credenciales de Usuario no son validas"
  ]
}
```

#### 3. RIPS sin Factura

**POST /api/PaquetesFevRips/CargarRipsSinFactura**

**Descripción:** Carga de RIPS sin factura electrónica asociada. Se utiliza para documentos no electrónicos.

**Autenticación:** Bearer Token (obtenido del login)

**Request Body:**
```json
{
  "rips": {
    "numDocumentoIdObligado": "901355357",
    "numFactura": null,
    "tipoNota": "RS",
    "numNota": "1",
    "usuarios": [
      {
        "tipoDocumentoIdentificacion": "CC",
        "numDocumentoIdentificacion": "1127606374",
        "tipoUsuario": "12",
        "fechaNacimiento": "1988-07-07",
        "codSexo": "M",
        "codPaisResidencia": "170",
        "codMunicipioResidencia": "76001",
        "codZonaTerritorialResidencia": "01",
        "incapacidad": "NO",
        "codPaisOrigen": "170",
        "consecutivo": 1,
        "servicios": {
          "consultas": [
            {
              "codPrestador": "760011269401",
              "fechaInicioAtencion": "2025-04-09 09:37",
              "numAutorizacion": null,
              "codConsulta": "890201",
              "modalidadGrupoServicioTecSal": "01",
              "grupoServicios": "02",
              "codServicio": 748,
              "finalidadTecnologiaSalud": "15",
              "causaMotivoAtencion": "38",
              "codDiagnosticoPrincipal": "Z012",
              "codDiagnosticoRelacionado1": null,
              "codDiagnosticoRelacionado2": null,
              "codDiagnosticoRelacionado3": null,
              "tipoDiagnosticoPrincipal": "01",
              "tipoDocumentoIdentificacion": "CC",
              "numDocumentoIdentificacion": "29126774",
              "vrServicio": 0,
              "conceptoRecaudo": "05",
              "valorPagoModerador": 0,
              "numFEVPagoModerador": null,
              "consecutivo": 1
            }
          ]
        }
      }
    ]
  },
  "xmlFevFile": null
}
```

**Notas importantes:**
- El campo `xmlFevFile` debe ser **null** para RIPS sin factura
- Se debe utilizar **RS** en el campo `tipoNota` haciendo referencia al documento no electrónico
- El campo `numNota` contiene la numeración del documento no electrónico
- El campo `numFactura` debe ser **null**

**Respuesta Exitosa (200):**
```json
{
  "ResultState": true,
  "ProcesoId": 8,
  "NumFactura": "1",
  "CodigoUnicoValidacion": "712cf437a5ac5a6a1503905ed9566f431cc425f4a55f0ebead67f0e816d88b30e6e2546dc90ba36abf3747cf8512b0d6",
  "FechaRadicacion": "2025-04-10T21:22:25.7325877+00:00",
  "RutaArchivos": null,
  "ResultadosValidacion": [
    {
      "Clase": "NOTIFICACION",
      "Codigo": "RVC017",
      "Descripcion": "El código de CUPS puede ser validado que corresponda a la cobertura o plan de beneficios informada en la factura electrónica de venta.",
      "Observaciones": "Verificar tabla de referencia Dato (890201)",
      "PathFuente": "usuarios[0].servicios.consultas[0].codConsulta",
      "Fuente": "Rips"
    }
  ]
}
```

**Respuesta Error (400):**
```json
{
  "resultState": false,
  "procesoId": 0,
  "numFactura": "",
  "codigoUnicoValidacion": "-",
  "codigoUnicoValidacionToShow": "No aplica a paquetes procesados en estado [RECHAZADO] o validaciones realizadas antes del envío al Ministerio de Salud y Protección Social",
  "fechaRadicacion": "2025-04-10T21:23:27.5220794+00:00",
  "rutaArchivos": null,
  "resultadosValidacion": [
    {
      "Clase": "RECHAZADO",
      "Codigo": "RVG01",
      "Descripcion": "Solo se podrá validar los RIPS si cumplen con la estructura establecida.",
      "Observaciones": "La propiedad [codPrestador] no cumple con el formato o tipo de dato requerido, Dato enviado [76001126940]",
      "PathFuente": "rips.usuarios[0].servicios.consultas[0].codPrestador",
      "Fuente": "Rips"
    }
  ]
}
```

#### 4. RIPS con Factura

**POST /api/PaquetesFevRips/CargarFevRips**

**Descripción:** Carga de RIPS asociado a una factura electrónica aceptada.

**Autenticación:** Bearer Token

**Request Body:**
```json
{
  "rips": {
    "numDocumentoIdObligado": "901355357",
    "numFactura": "SETP990000103",
    "tipoNota": null,
    "numNota": null,
    "usuarios": [...]
  },
  "xmlFevFile": "<base64_encoded_zip>"
}
```

**Notas importantes:**
- El campo `xmlFevFile` está relacionado con el campo `attacheddocument` de la respuesta de una factura aceptada en el API
- Si posee el zip, debe convertirlo en Base64 y añadir el string obtenido
- El campo `numFactura` debe contener el correlativo y numeración juntos, sin caracteres especiales
- El NIT en RIPS debe coincidir con el NIT de la factura electrónica
- El código del prestador debe estar relacionado con el `numDocumentoIdObligado`

**Clases de Validación:**
- **NOTIFICACION**: Avisos informativos que no impiden el proceso
- **RECHAZADO**: Errores que impiden el procesamiento del RIPS

**Códigos de Validación Comunes:**

| Código | Descripción |
|--------|-------------|
| RVC001 | El número de NIT del facturador debe coincidir con el NIT registrado en la FEV |
| RVC011 | El código del facturador electrónico debe estar en tabla IPSCodHabilitación o IPSnoREPS |
| RVC012 | El código del prestador debe estar relacionado con el numDocumentoIdObligado |
| RVC017 | El código de CUPS puede ser validado con la cobertura informada |
| RVC019 | El código de CUPS se puede validar con el diagnóstico principal |
| RVC051 | La finalidad informada se puede validar con el sexo y la edad del usuario |
| RVC059 | El código de CUPS puede ser validado con el grupo de servicio |
| RVC061 | Cuando no aplique pago moderador se debe informar cero (0) |
| RVG01 | Solo se podrá validar los RIPS si cumplen con la estructura establecida |

### Estructura de Datos RIPS

#### Usuario (Paciente)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `tipoDocumentoIdentificacion` | string | Tipo de documento (CC, TI, CE, etc.) |
| `numDocumentoIdentificacion` | string | Número de documento |
| `tipoUsuario` | string | Código del tipo de usuario |
| `fechaNacimiento` | string (date) | Fecha de nacimiento YYYY-MM-DD |
| `codSexo` | string | Código de sexo (M, F) |
| `codPaisResidencia` | string | Código del país de residencia |
| `codMunicipioResidencia` | string | Código del municipio de residencia |
| `codZonaTerritorialResidencia` | string | Código de zona territorial |
| `incapacidad` | string | Indica incapacidad (SI, NO) |
| `codPaisOrigen` | string | Código del país de origen |
| `consecutivo` | integer | Consecutivo del usuario |
| `servicios` | object | Objeto con los servicios prestados |

#### Servicios - Consultas

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `codPrestador` | string | Código del prestador habilitado |
| `fechaInicioAtencion` | string (datetime) | Fecha y hora de inicio de atención |
| `numAutorizacion` | string | Número de autorización |
| `codConsulta` | string | Código CUPS de la consulta |
| `modalidadGrupoServicioTecSal` | string | Modalidad del grupo de servicio |
| `grupoServicios` | string | Grupo de servicios |
| `codServicio` | integer | Código del servicio |
| `finalidadTecnologiaSalud` | string | Finalidad de la tecnología en salud |
| `causaMotivoAtencion` | string | Causa o motivo de atención |
| `codDiagnosticoPrincipal` | string | Código CIE-10 del diagnóstico principal |
| `codDiagnosticoRelacionado1` | string | Código diagnóstico relacionado 1 |
| `codDiagnosticoRelacionado2` | string | Código diagnóstico relacionado 2 |
| `codDiagnosticoRelacionado3` | string | Código diagnóstico relacionado 3 |
| `tipoDiagnosticoPrincipal` | string | Tipo de diagnóstico principal |
| `tipoDocumentoIdentificacion` | string | Tipo de documento del profesional |
| `numDocumentoIdentificacion` | string | Número de documento del profesional |
| `vrServicio` | number | Valor del servicio |
| `conceptoRecaudo` | string | Concepto de recaudo |
| `valorPagoModerador` | number | Valor del pago moderador |
| `numFEVPagoModerador` | string | Número de FEV del pago moderador |
| `consecutivo` | integer | Consecutivo de la consulta |

#### Servicios - Procedimientos

Similar a consultas, con campos adicionales específicos de procedimientos.

#### Servicios - Urgencias

Similar a consultas, con campos adicionales específicos de urgencias.

#### Servicios - Hospitalización

Similar a consultas, con campos adicionales específicos de hospitalización.

#### Servicios - Recién Nacidos

Campos específicos para atención de recién nacidos.

#### Servicios - Medicamentos

Información sobre medicamentos suministrados.

#### Servicios - Otros Servicios

Otros servicios de salud prestados.

### Tablas de Referencia

Para validaciones correctas de RIPS, se deben consultar las siguientes tablas de referencia del Ministerio de Salud:

- Tabla de tipos de documento de identificación
- Tabla de tipos de usuario
- Tabla de códigos CUPS
- Tabla de códigos CIE-10
- Tabla de coberturas y planes de beneficios
- Tabla de códigos de prestadores habilitados (REPS)
- Tabla de finalidades de tecnología en salud
- Tabla de causas de atención
- Tabla de grupos de servicios

## Notas Importantes

1. **Autenticación**: Todos los endpoints de RIPS requieren autenticación mediante Bearer Token obtenido del login SISPRO.

2. **Validaciones**: El sistema RIPS realiza validaciones exhaustivas según normativa del Ministerio de Salud y Protección Social de Colombia.

3. **MIPRES**: El sistema MIPRES (Mi Prescripción) es obligatorio para ciertos medicamentos y dispositivos médicos. Los números de prescripción deben separarse con punto y coma (;).

4. **Código Único de Validación (CUV)**: Es generado por el Ministerio de Salud una vez el RIPS es aceptado.

5. **Factura Electrónica + RIPS**: Para sector salud, la factura electrónica debe estar vinculada con el RIPS correspondiente mediante el campo `attacheddocument`.

6. **Estados de Procesamiento**:
   - ACEPTADO: RIPS procesado correctamente
   - RECHAZADO: RIPS rechazado por errores de validación
   - NOTIFICACION: Avisos que no impiden procesamiento

7. **Tipos de Nota RIPS**:
   - **RS**: Documento no electrónico (RIPS sin factura)
   - **null**: Cuando está asociado a factura electrónica (RIPS con factura)

## Soporte y Documentación Adicional

Para más información sobre normativa y tablas de referencia, consultar:
- Resolución 506 de 2013 (Anexo Técnico RIPS)
- Resolución 3374 de 2000
- Documentación oficial del Ministerio de Salud y Protección Social
- Sistema SISPRO (Sistema Integrado de Información de la Protección Social)
