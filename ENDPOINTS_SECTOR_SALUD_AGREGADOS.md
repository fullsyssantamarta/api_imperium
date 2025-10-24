# Endpoints de Sector Salud Agregados

**Fecha:** 24 de octubre de 2025  
**Archivo actualizado:** `/root/apidian/storage/api-docs/api-docs.json` (99 KB)

## Nuevos Endpoints de Facturación Sector Salud

### 1. Factura Capita Inicial

**Endpoint:** `POST /ubl2.1/invoice-health-capita-initial/{testSetId}`

**Características:**
- Tipo: SS-RECAUDO
- Modalidad: Inicio de contrato por cápita
- **NO contiene atención de pacientes**
- XML incluye sección `InformationContentProviderParty`
- El `attacheddocument` debe usarse en el RIPS como en el ejemplo "RIPS Capita inicial"

**Campos especiales:**
```json
{
  "health_type_operation_id": 2,
  "information_content_provider": {
    "identification_number": "900123456",
    "name": "PRESTADOR EJEMPLO",
    "contact_name": "Juan Pérez",
    "contact_telephone": "3001234567",
    "contact_email": "contacto@prestador.com"
  }
}
```

**Cuándo usar:** 
- Facturación inicial de contratos por capita
- Sin servicios prestados aún
- Registro del inicio de relación contractual

---

### 2. Factura Capita Periodo

**Endpoint:** `POST /ubl2.1/invoice-health-capita-period/{testSetId}`

**Características:**
- Tipo: SS-CUFE
- **Contiene atención de pacientes**
- JSON y XML similares a factura de salud estándar
- El `attacheddocument` debe usarse en el RIPS como en el ejemplo "RIPS Capita Periodo"

**Campos requeridos:**
```json
{
  "health_fields": {
    "invoice_period_start_date": "2025-01-01",
    "invoice_period_end_date": "2025-01-31",
    "health_type_operation_id": 1,
    "users_info": [
      {
        "provider_code": "AF-0000500-85-XX-001",
        "identification_number": "123456789",
        "surname": "PEREZ",
        "first_name": "JUAN",
        ...
      }
    ]
  }
}
```

**Cuándo usar:**
- Facturación periódica de contratos por capita
- Incluye servicios prestados en el periodo
- Con información detallada de pacientes atendidos

---

### 3. Factura Múltiples Pacientes

**Endpoint:** `POST /ubl2.1/invoice-health-multiple-patients/{testSetId}`

**Características:**
- Factura única con múltiples pacientes
- Array `users_info` puede contener N pacientes
- Cada paciente con sus servicios, MIPRES, autorizaciones y valores
- Eficiente para consolidar atención de múltiples usuarios

**Ejemplo:**
```json
{
  "health_fields": {
    "invoice_period_start_date": "2025-01-01",
    "invoice_period_end_date": "2025-01-31",
    "health_type_operation_id": 1,
    "print_users_info_to_pdf": true,
    "users_info": [
      {
        "identification_number": "123456789",
        "surname": "PEREZ",
        "first_name": "JUAN",
        "co_payment": "5000.00"
      },
      {
        "identification_number": "987654321",
        "surname": "GOMEZ",
        "first_name": "MARIA",
        "moderating_fee": "8000.00"
      },
      {
        "identification_number": "456789123",
        "surname": "RODRIGUEZ",
        "first_name": "CARLOS",
        "recovery_fee": "12000.00"
      }
    ]
  }
}
```

**Cuándo usar:**
- Consolidar facturación de múltiples pacientes en un solo documento
- Atención ambulatoria con varios usuarios
- Optimizar número de documentos electrónicos

---

## Notas Crédito y Débito Sector Salud

### 4. Nota Crédito Sector Salud

**Endpoint:** `POST /ubl2.1/credit-note-health/{testSetId}`

**Tags:** `["Notas Crédito y Débito", "Sector Salud"]`

**Características:**
- Debe referenciar una factura de salud previamente emitida
- Puede incluir `health_fields` si aplica a servicios específicos
- Incluye información de pacientes afectados por el ajuste

**Estructura:**
```json
{
  "number": 1,
  "type_document_id": 4,
  "prefix": "NCSS",
  "billing_reference": {
    "number": "SETP990000105",
    "uuid": "abc123def456...",
    "issue_date": "2025-01-15"
  },
  "discrepancy_response": {
    "code": "1",
    "description": "Anulación total de servicios de salud facturados"
  },
  "customer": {
    "identification_number": "900123456",
    "name": "EPS EJEMPLO"
  },
  "health_fields": {
    "invoice_period_start_date": "2025-01-01",
    "invoice_period_end_date": "2025-01-15",
    "health_type_operation_id": 1,
    "users_info": [
      {
        "provider_code": "AF-0000500-85-XX-001",
        "identification_number": "123456789",
        "surname": "PEREZ",
        "first_name": "JUAN"
      }
    ]
  },
  "legal_monetary_totals": {
    "line_extension_amount": 50000,
    "payable_amount": 50000
  },
  "credit_note_lines": []
}
```

**Conceptos comunes:**
- Código 1: Anulación total
- Código 2: Anulación parcial
- Código 3: Descuento o rebaja
- Código 4: Devolución de servicios
- Código 5: Otros

**Cuándo usar:**
- Anular factura de salud total o parcialmente
- Corregir valores facturados
- Descuentos posteriores a facturación
- Devolución por glosas de la EPS

---

### 5. Nota Débito Sector Salud

**Endpoint:** `POST /ubl2.1/debit-note-health/{testSetId}`

**Tags:** `["Notas Crédito y Débito", "Sector Salud"]`

**Características:**
- Ajusta valores adicionales en facturas de salud
- Debe referenciar una factura de salud previamente emitida
- Incluye `health_fields` con información de pacientes

**Estructura:**
```json
{
  "number": 1,
  "type_document_id": 5,
  "prefix": "NDSS",
  "billing_reference": {
    "number": "SETP990000105",
    "uuid": "abc123def456...",
    "issue_date": "2025-01-15"
  },
  "discrepancy_response": {
    "code": "2",
    "description": "Ajuste por servicios adicionales de salud no facturados inicialmente"
  },
  "customer": {
    "identification_number": "900123456",
    "name": "EPS EJEMPLO"
  },
  "health_fields": {
    "invoice_period_start_date": "2025-01-01",
    "invoice_period_end_date": "2025-01-15",
    "health_type_operation_id": 1,
    "users_info": [
      {
        "provider_code": "AF-0000500-85-XX-001",
        "identification_number": "123456789",
        "surname": "PEREZ",
        "first_name": "JUAN",
        "moderating_fee": "15000.00"
      }
    ]
  },
  "legal_monetary_totals": {
    "line_extension_amount": 25000,
    "payable_amount": 25000
  },
  "debit_note_lines": []
}
```

**Conceptos comunes:**
- Código 1: Intereses
- Código 2: Gastos por cobrar
- Código 3: Cambio del valor
- Código 4: Otros

**Cuándo usar:**
- Servicios adicionales no facturados inicialmente
- Ajustes por diferencias en cuotas moderadoras
- Cargos adicionales autorizados
- Actualización de valores por glosas resueltas favorablemente

---

## Cambios en Schemas

### CreditNote - Actualizado

Ahora incluye campo opcional `health_fields`:

```json
{
  "health_fields": {
    "allOf": [
      { "$ref": "#/components/schemas/HealthFields" }
    ],
    "description": "Campos específicos para notas crédito del sector salud. Opcional, incluir solo si aplica a factura de salud."
  }
}
```

### DebitNote - Actualizado

Ahora es un schema completo (antes solo tenía 2 campos):

```json
{
  "number": { "type": "integer" },
  "type_document_id": { "type": "integer", "example": 5 },
  "prefix": { "type": "string" },
  "billing_reference": { ... },
  "discrepancy_response": { ... },
  "customer": { ... },
  "legal_monetary_totals": { ... },
  "debit_note_lines": [ ... ],
  "health_fields": {
    "allOf": [
      { "$ref": "#/components/schemas/HealthFields" }
    ],
    "description": "Campos específicos para notas débito del sector salud. Opcional, incluir solo si aplica a factura de salud."
  }
}
```

---

## Resumen de Endpoints Totales

### Antes:
- Factura estándar: 1 endpoint
- Nota crédito: 1 endpoint
- Nota débito: 1 endpoint
- **Total sector salud: 0 endpoints específicos**

### Ahora:
- **Factura Capita Inicial**: 1 endpoint
- **Factura Capita Periodo**: 1 endpoint
- **Factura Múltiples Pacientes**: 1 endpoint
- **Nota Crédito Salud**: 1 endpoint
- **Nota Débito Salud**: 1 endpoint
- **Total sector salud: 5 endpoints nuevos**

### Gran Total API:
- **Antes:** ~24 endpoints
- **Ahora:** ~29 endpoints
- **Incremento:** +5 endpoints sector salud

---

## Flujo de Trabajo Sector Salud

### Escenario 1: Contrato por Capita

1. **Inicio del contrato:**
   ```
   POST /ubl2.1/invoice-health-capita-initial/{testSetId}
   - Sin pacientes
   - Registro contractual
   ```

2. **Facturación mensual:**
   ```
   POST /ubl2.1/invoice-health-capita-period/{testSetId}
   - Con pacientes atendidos
   - Servicios del periodo
   ```

3. **Ajustes si necesario:**
   ```
   POST /ubl2.1/credit-note-health/{testSetId}  (devoluciones)
   POST /ubl2.1/debit-note-health/{testSetId}   (cargos adicionales)
   ```

### Escenario 2: Atención Ambulatoria

1. **Facturación consolidada:**
   ```
   POST /ubl2.1/invoice-health-multiple-patients/{testSetId}
   - Múltiples pacientes en una factura
   - Optimización de documentos
   ```

2. **RIPS asociado:**
   ```
   POST /rips/api/PaquetesFevRips/CargarFevRips
   - xmlFevFile con attacheddocument de la factura
   - Usuarios coinciden con users_info
   ```

### Escenario 3: Glosas y Ajustes

1. **Factura original:**
   ```
   POST /ubl2.1/invoice/{testSetId}
   - Con health_fields
   ```

2. **Glosa rechazada por EPS:**
   ```
   POST /ubl2.1/credit-note-health/{testSetId}
   - Anulación parcial de servicios glosados
   ```

3. **Glosa aceptada:**
   ```
   POST /ubl2.1/debit-note-health/{testSetId}
   - Ajuste de valores adicionales autorizados
   ```

---

## Validaciones Importantes

### Factura Capita Inicial
✅ NO debe incluir `health_fields.users_info`  
✅ Debe incluir `information_content_provider`  
✅ `health_type_operation_id` = 2 (capita)  

### Factura Capita Periodo
✅ DEBE incluir `health_fields.users_info`  
✅ Periodo de facturación requerido  
✅ Pacientes con servicios prestados  

### Notas Crédito/Débito Salud
✅ `billing_reference` debe apuntar a factura de salud válida  
✅ CUFE de factura referenciada requerido  
✅ `health_fields` opcional pero recomendado  
✅ Pacientes en `users_info` deben coincidir con factura original  

---

## Integración con RIPS

Todos los tipos de factura sector salud **DEBEN** tener su RIPS asociado:

| Tipo Factura | Tipo RIPS | Campo xmlFevFile |
|--------------|-----------|------------------|
| Capita Inicial | RIPS Capita Inicial | attacheddocument de factura |
| Capita Periodo | RIPS Capita Periodo | attacheddocument de factura |
| Múltiples Pacientes | RIPS con factura | attacheddocument de factura |
| Nota Crédito Salud | RIPS ajuste | attacheddocument de NC |
| Nota Débito Salud | RIPS ajuste | attacheddocument de ND |

---

## Próximos Pasos

Para utilizar estos nuevos endpoints:

1. ✅ Documentación actualizada en `/documentation`
2. ✅ Schemas completos en `api-docs.json`
3. ✅ Ejemplos incluidos en cada endpoint
4. ⏳ Probar endpoints con Postman/Swagger UI
5. ⏳ Validar respuestas DIAN
6. ⏳ Integrar con sistema RIPS

---

**Archivo actualizado:** `/root/apidian/storage/api-docs/api-docs.json`  
**Tamaño:** 99 KB  
**Compatible con:** Swagger UI 3.0, OpenAPI 3.0.0
