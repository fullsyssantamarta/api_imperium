# Documentación Swagger - APIDIAN

## Información General

La API DIAN cuenta con documentación Swagger/OpenAPI 3.0 completa y actualizada que describe todos los endpoints disponibles para la gestión de documentos electrónicos en Colombia.

## Acceso a la Documentación

### URL de Acceso

- **Producción:** https://api.imperiumfevsrips.net/documentation
- **Local:** http://localhost/documentation

### Interfaz Swagger UI

La documentación está disponible a través de Swagger UI, una interfaz interactiva que permite:

- Visualizar todos los endpoints disponibles
- Ver los parámetros requeridos y opcionales
- Consultar ejemplos de peticiones y respuestas
- Probar endpoints directamente desde el navegador
- Ver esquemas de datos detallados

## Estructura de la Documentación

### Tags Principales

La API está organizada en las siguientes categorías:

#### 1. Configuración Básica
Endpoints para configuración inicial del sistema:
- `POST /config/{nit}/{dv}` - Crear/actualizar empresa
- `PUT /config/software` - Configurar software de FE
- `PUT /config/softwarepayroll` - Configurar software de nómina
- `PUT /config/softwareeqdocs` - Configurar software de docs equivalentes
- `PUT /config/certificate` - Cargar certificado digital
- `PUT /config/resolution` - Configurar resoluciones

#### 2. Facturación Electrónica
- `POST /invoice/{testSetId}` - Crear factura electrónica
- Soporte para todos los tipos de facturas (FV, FV AIU, FV Exportación, etc.)

#### 3. Documentos Equivalentes
- POS (Point of Sale)
- Tiquetes de transporte terrestre
- Servicios públicos domiciliarios
- Boletas de ingreso a cine
- Y más...

#### 4. Documentos Soporte
- `POST /support-document/{testSetId}` - Documentos soporte de adquisiciones

#### 5. Notas Crédito y Débito
- `POST /credit-note/{testSetId}` - Notas crédito
- `POST /debit-note/{testSetId}` - Notas débito

#### 6. Nómina Electrónica
- `POST /payroll/{testSetId}` - Documentos de nómina
- Soporte para ajustes de nómina

#### 7. RADIAN
- `POST /radian-event` - Eventos RADIAN (acuse, aceptación, rechazo, etc.)

#### 8. RIPS
Registro Individual de Prestación de Servicios de Salud:
- Configuración de proveedores
- Registro de pacientes
- Citas médicas
- Servicios prestados
- Generación de documentos RIPS

#### 9. Consultas
- `GET /documents` - Listar documentos
- `GET /documents/{prefix}/{number}` - Consultar por consecutivo
- `GET /pdf/{cufe}` - Descargar PDF
- `GET /xml/{cufe}` - Descargar XML

#### 10. Clientes e Items
- `POST /customer` - Crear clientes
- `POST /item` - Crear productos/servicios

#### 11. Utilitarios
- `GET /trm` - Consultar TRM
- Envío de emails
- Regeneración de PDF
- Consulta de estados

## Autenticación

### Token Bearer

La API utiliza autenticación mediante token Bearer:

```
Authorization: Bearer {tu_token_api}
```

### Obtención del Token

El token se obtiene al crear/configurar una empresa con el endpoint:
```
POST /ubl2.1/config/{nit}/{dv}
```

Este endpoint **NO requiere autenticación** y devuelve un token en la respuesta que debe usarse en todas las peticiones subsecuentes.

### Ejemplo de Uso

```bash
# 1. Crear empresa (sin auth)
curl -X POST "https://api.imperiumfevsrips.net/api/ubl2.1/config/89008003/2" \
  -H "Content-Type: application/json" \
  -d '{
    "type_document_identification_id": 3,
    "business_name": "MI EMPRESA SAS",
    "email": "contacto@miempresa.com",
    ...
  }'

# Respuesta:
# {
#   "success": true,
#   "token": "abc123...",
#   ...
# }

# 2. Usar token en peticiones subsecuentes
curl -X POST "https://api.imperiumfevsrips.net/api/ubl2.1/invoice/" \
  -H "Authorization: Bearer abc123..." \
  -H "Content-Type: application/json" \
  -d '{...}'
```

## Schemas de Datos

### Principales Modelos

#### CompanyConfiguration
Configuración de empresa con todos los campos requeridos para registro ante DIAN.

#### Invoice
Estructura completa de factura electrónica con:
- Datos del cliente
- Líneas de factura (productos/servicios)
- Totales monetarios
- Impuestos
- Formas de pago

#### CreditNote / DebitNote
Notas de ajuste con referencia a documento original.

#### Customer
Información completa del cliente/proveedor.

#### Item
Productos y servicios con códigos y precios.

#### RadianEvent
Eventos de aceptación/rechazo de documentos.

## Ejemplos de Uso

### 1. Configurar Empresa

```json
POST /ubl2.1/config/900123456/7

{
  "type_document_identification_id": 3,
  "type_organization_id": 1,
  "type_regime_id": 2,
  "type_liability_id": 14,
  "business_name": "MI EMPRESA SAS",
  "merchant_registration": "12345678",
  "municipality_id": 149,
  "address": "CRA 7 # 32-16",
  "phone": "3001234567",
  "email": "contacto@miempresa.com"
}
```

### 2. Configurar Resolución de Factura

```json
PUT /ubl2.1/config/resolution

{
  "type_document_id": 1,
  "prefix": "FV",
  "resolution": "18760000001",
  "resolution_date": "2024-01-01",
  "technical_key": "fc8eac422eba16e22ffd8c6f94b3f40a6e38162c",
  "from": 1,
  "to": 5000000,
  "generated_to_date": 0,
  "date_from": "2024-01-01",
  "date_to": "2029-12-31"
}
```

### 3. Crear Factura

```json
POST /ubl2.1/invoice/

{
  "number": 1,
  "type_document_id": 1,
  "date": "2024-10-24",
  "time": "10:30:00",
  "customer": {
    "identification_number": "900456789",
    "name": "CLIENTE EJEMPLO SAS",
    "phone": "3009876543",
    "address": "CL 100 # 15-20",
    "email": "cliente@ejemplo.com",
    "municipality_id": 149
  },
  "legal_monetary_totals": {
    "line_extension_amount": 100000,
    "tax_exclusive_amount": 100000,
    "tax_inclusive_amount": 119000,
    "payable_amount": 119000
  },
  "invoice_lines": [
    {
      "unit_measure_id": 642,
      "invoiced_quantity": 1,
      "line_extension_amount": 100000,
      "description": "Servicio de consultoría",
      "code": "SERV001",
      "price_amount": 100000,
      "tax_totals": [
        {
          "tax_id": 1,
          "tax_amount": 19000,
          "taxable_amount": 100000,
          "percent": 19
        }
      ]
    }
  ]
}
```

## Códigos de Respuesta HTTP

| Código | Descripción |
|--------|-------------|
| 200 | Petición exitosa |
| 201 | Recurso creado exitosamente |
| 400 | Petición mal formada |
| 401 | No autorizado (token inválido/ausente) |
| 403 | Acceso prohibido |
| 404 | Recurso no encontrado |
| 422 | Error de validación |
| 500 | Error interno del servidor |

## Tipos de Documentos (type_document_id)

| ID | Tipo de Documento |
|----|-------------------|
| 1 | Factura Electrónica (FE) |
| 2 | Factura Exportación |
| 3 | Factura AIU |
| 4 | Nota Crédito |
| 5 | Nota Débito |
| 9 | Nómina Individual |
| 10 | Nota de Ajuste Nómina |
| 11 | Documento Soporte |
| 13 | NC Documento Soporte |
| 15 | POS |
| 16 | Boleta Ingreso Cine |
| 19 | Transporte Terrestre |
| 24 | Servicios Públicos |
| 25 | ND Documento Equivalente |
| 26 | NC Documento Equivalente |

## IDs de Tablas Paramétricas

### type_document_identification_id
- 3: NIT
- 6: Cédula de Ciudadanía
- 13: Cédula de Extranjería
- 31: NIT de otro país

### type_organization_id
- 1: Persona Jurídica
- 2: Persona Natural

### type_regime_id
- 1: Régimen Simple
- 2: Régimen Común

### type_liability_id (Responsabilidades fiscales)
- 14: Gran Contribuyente
- 48: No responsable de IVA
- 49: Responsable de IVA

### tax_id (Impuestos)
- 1: IVA
- 4: INC (Impuesto al Consumo)
- 5: Retención IVA
- 6: Retención Renta
- 7: Retención ICA

## Ambientes DIAN

### Habilitación (Pruebas)
- type_environment_id: 2
- URL: https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc
- Technical Key: fc8eac422eba16e22ffd8c6f94b3f40a6e38162c

### Producción
- type_environment_id: 1
- URL: https://vpfe.dian.gov.co/WcfDianCustomerServices.svc
- Technical Key: Consultar con endpoint getnumberingrange

## Regeneración de Documentación

Para regenerar la documentación Swagger desde el código:

```bash
# Dentro del contenedor
docker exec php_api php artisan l5-swagger:generate

# O desde el host
cd /root/apidian
docker exec php_api php artisan l5-swagger:generate
```

## Archivos de Documentación

- **Configuración:** `/root/apidian/config/l5-swagger.php`
- **JSON generado:** `/root/apidian/storage/api-docs/api-docs.json`
- **Vista Blade:** `/root/apidian/resources/views/vendor/l5-swagger/index.blade.php`
- **Anotaciones:** `/root/apidian/app/Http/Controllers/Api/*.php`

## Colección Postman

Además de Swagger, existe una colección completa de Postman disponible en:
```
/root/apidian/apidian.json
```

Esta colección incluye:
- Todos los endpoints documentados
- Ejemplos de peticiones
- Variables de entorno
- Scripts de pre-request y tests

Puedes importarla directamente en Postman para probar la API.

## Soporte y Contacto

**Desarrollador:** Fulvio Leonardo Badillo Caseres  
**Email:** fullsyssantamarta@gmail.com  
**Empresa:** Fullsys Tecnología  
**Ubicación:** Santa Marta, Colombia

## Referencias

- [Documentación DIAN](https://www.dian.gov.co/impuestos/factura-electronica)
- [Especificación UBL 2.1](http://www.datypic.com/sc/ubl21/ss.html)
- [OpenAPI Specification](https://swagger.io/specification/)

---

**Última actualización:** 24 de octubre de 2025  
**Versión API:** 2.1.0
