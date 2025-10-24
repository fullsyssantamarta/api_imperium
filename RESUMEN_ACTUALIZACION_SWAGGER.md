# Resumen de Actualización Swagger/OpenAPI - Sector Salud y RIPS

**Fecha:** 24 de octubre de 2025  
**Archivos actualizados:**
- `/root/apidian/storage/api-docs/openapi-complete.json`
- `/root/apidian/storage/api-docs/api-docs.json`
- `/root/apidian/DOCUMENTACION_SWAGGER_COMPLETA.md` (nueva documentación detallada)

## Cambios Realizados

### 1. Nuevos Tags (Categorías)

- **Sector Salud**: Facturación electrónica para el sector salud con información de pacientes, MIPRES y servicios médicos
- **RIPS**: Actualizado con descripción completa - API RIPS para validación y envío al Ministerio de Salud

### 2. Endpoints Agregados

#### API RIPS (3 nuevos endpoints)

1. **GET /rips/BasicHealth**
   - Verificar estado del servicio RIPS
   - Sin autenticación
   - Retorna información de versión del servicio

2. **POST /rips/api/auth/LoginSISPRO**
   - Login en SISPRO para acceso a RIPS
   - Sin autenticación inicial
   - Retorna token JWT para usar en endpoints RIPS
   - Requiere: tipo documento, número documento, clave, NIT

3. **POST /rips/api/PaquetesFevRips/CargarRipsSinFactura**
   - Cargar RIPS sin factura electrónica asociada
   - Requiere autenticación JWT RIPS
   - Para documentos no electrónicos (tipoNota: "RS")
   - Valida estructura RIPS según normativa Ministerio de Salud

4. **POST /rips/api/PaquetesFevRips/CargarFevRips**
   - Cargar RIPS con factura electrónica
   - Requiere autenticación JWT RIPS
   - xmlFevFile debe contener el attacheddocument en base64
   - NIT debe coincidir con la factura electrónica

### 3. Schemas (Estructuras de Datos) Agregados

#### Sector Salud

1. **HealthFields**
   - Campos específicos para facturación del sector salud
   - Campos requeridos:
     * invoice_period_start_date
     * invoice_period_end_date
     * health_type_operation_id
     * users_info (array de pacientes)

2. **HealthPatientInfo**
   - Información completa del paciente/usuario de salud
   - Incluye 18 campos:
     * Identificación del paciente
     * Nombres y apellidos completos
     * Código del prestador
     * Tipo de usuario de salud
     * Método de pago
     * Cobertura/EPS
     * Autorizaciones
     * MIPRES (prescripciones)
     * MIPRES delivery (entregas)
     * Números de contrato y póliza
     * Valores: copago, cuota moderadora, cuota de recuperación, pago compartido

#### RIPS

3. **RipsPackage**
   - Paquete completo RIPS
   - Contiene: rips (datos) + xmlFevFile (opcional)

4. **RipsData**
   - Datos principales del RIPS
   - NIT obligado, número factura, tipo nota, usuarios

5. **RipsUsuario**
   - Usuario/paciente en el RIPS
   - 12 campos requeridos: documento, tipo usuario, fecha nacimiento, sexo, residencia, etc.
   - Contiene objeto "servicios" con array de consultas/procedimientos

6. **RipsServicios**
   - Servicios de salud prestados
   - Arrays para: consultas, procedimientos, urgencias, hospitalización, recién nacidos, medicamentos, otros

7. **RipsConsulta**
   - Registro detallado de consulta médica
   - 21 campos incluyendo:
     * Código prestador habilitado
     * Códigos CUPS
     * Diagnósticos CIE-10
     * Valores y conceptos de recaudo
     * Información del profesional

8. **RipsProcedimiento**
   - Registro de procedimiento médico
   - Similar a consulta con campos específicos de procedimientos

9. **RipsResponse**
   - Respuesta exitosa del Ministerio de Salud
   - Incluye:
     * Código Único de Validación (CUV)
     * Fecha de radicación
     * Array de validaciones (notificaciones)

10. **RipsResponseError**
    - Respuesta de error/rechazo
    - Incluye validaciones que causaron el rechazo

11. **RipsValidacion**
    - Detalle de cada validación
    - Clase: NOTIFICACION o RECHAZADO
    - Códigos: RVC001, RVC017, RVG01, etc.
    - Descripción, observaciones, ruta del campo

### 4. Autenticación Actualizada

- **Bearer**: Token API APIDIAN (existente)
- **RipsBearer**: Token JWT SISPRO (nuevo)
  - Se obtiene del login SISPRO
  - Formato JWT
  - Requerido para endpoints RIPS

### 5. Invoice Schema Actualizado

El schema `Invoice` ahora incluye campo opcional:

```json
{
  "health_fields": {
    "invoice_period_start_date": "2025-01-01",
    "invoice_period_end_date": "2025-01-29",
    "health_type_operation_id": 1,
    "print_users_info_to_pdf": true,
    "users_info": [...]
  }
}
```

Este campo es **requerido solo para facturas del sector salud**.

### 6. Endpoint de Invoice Actualizado

**POST /ubl2.1/invoice/{testSetId}**
- Ahora tiene 2 tags: "Facturación Electrónica" y "Sector Salud"
- Descripción actualizada menciona health_fields para sector salud

## Códigos de Validación RIPS Documentados

### Códigos Comunes

| Código | Tipo | Descripción |
|--------|------|-------------|
| RVC001 | RECHAZADO | NIT del facturador debe coincidir con NIT de la FEV |
| RVC011 | RECHAZADO | Código facturador debe estar en IPSCodHabilitación o IPSnoREPS |
| RVC012 | RECHAZADO | Código prestador debe relacionarse con numDocumentoIdObligado |
| RVC017 | NOTIFICACION | Código CUPS puede validarse con cobertura informada |
| RVC019 | NOTIFICACION | Código CUPS puede validarse con diagnóstico principal |
| RVC051 | NOTIFICACION | Finalidad puede validarse con sexo y edad del usuario |
| RVC059 | NOTIFICACION | Código CUPS puede validarse con grupo de servicio |
| RVC061 | RECHAZADO | Cuando no aplique pago moderador debe informarse cero |
| RVG01 | RECHAZADO | RIPS no cumple con estructura establecida |

## Documentación Adicional Creada

### Archivo Markdown Completo

**Ubicación:** `/root/apidian/DOCUMENTACION_SWAGGER_COMPLETA.md`

Este archivo contiene:

1. **Resumen de Endpoints Documentados**
   - Sector Salud
   - API RIPS

2. **Descripción Detallada de Campos**
   - Tablas con todos los campos health_fields
   - Tablas con todos los campos users_info (pacientes)
   - Explicación de cada campo RIPS

3. **Variaciones de Factura Sector Salud**
   - SendInvoice-TestSetId Health
   - Health Multiples pacientes
   - Capita Inicial
   - Capita Periodo

4. **Estructura Completa RIPS**
   - Usuario (Paciente)
   - Servicios - Consultas
   - Servicios - Procedimientos
   - Servicios - Urgencias
   - Servicios - Hospitalización
   - Servicios - Recién Nacidos
   - Servicios - Medicamentos
   - Servicios - Otros Servicios

5. **Tablas de Referencia**
   - Tipos de documento
   - Tipos de usuario
   - Códigos CUPS
   - Códigos CIE-10
   - Coberturas y planes
   - Prestadores habilitados (REPS)
   - Finalidades tecnología salud
   - Causas de atención

6. **Notas Importantes**
   - Autenticación RIPS
   - Validaciones normativa Ministerio
   - MIPRES obligatorio
   - CUV (Código Único de Validación)
   - Estados de procesamiento
   - Tipos de nota RIPS

## Información Técnica

### Tamaño de Archivos

- `api-docs.json`: 80 KB (antes: ~10 KB)
- `openapi-complete.json`: 80 KB
- Incremento: ~70 KB de documentación nueva

### Total de Endpoints Documentados

- **Antes**: ~20 endpoints
- **Ahora**: ~24 endpoints
- **Nuevos**: 4 endpoints RIPS

### Total de Schemas

- **Antes**: ~15 schemas
- **Ahora**: ~26 schemas
- **Nuevos**: 11 schemas para Sector Salud y RIPS

## Siguiente Paso

Para ver la documentación actualizada:

1. Acceder a `/documentation` en el navegador
2. Verificar que aparezcan las nuevas secciones:
   - Tag "Sector Salud"
   - Tag "RIPS"
3. Probar los endpoints RIPS con ejemplos incluidos

## Compatibilidad

- **OpenAPI Version**: 3.0.0
- **Compatible con**: Swagger UI, Postman, Insomnia, ReDoc
- **Formato**: JSON válido

## Fuentes de Información

La documentación se extrajo de:
- `/root/apidian/apidian.json` (Colección Postman)
  - Sección 11 - Sector Salud (línea 11143)
  - Sección 15 - API RIPS (línea 17476)
- Normativa Ministerio de Salud y Protección Social de Colombia
- Resolución 506 de 2013 (Anexo Técnico RIPS)
- Sistema SISPRO (Sistema Integrado de Información de la Protección Social)
