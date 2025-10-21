# API DIAN - Sistema de Facturación Electrónica Colombia# **APIDIAN 2025**



API REST para integración con el sistema de facturación electrónica de la DIAN (Dirección de Impuestos y Aduanas Nacionales) de Colombia, basado en APIDIAN 2025.## Acerca de FACTURALATAM



## DescripciónEstamos enfocados en crear herramientas y soluciones para desarrolladores de software, empresas medianas y pequeñas, tenemos un sueño, queremos democratizar la facturación electrónica.



API DIAN es un sistema completo para la generación, firma y transmisión de documentos electrónicos según las especificaciones de la DIAN Colombia. Soporta UBL 2.1 y todos los tipos de documentos electrónicos requeridos por la legislación colombiana.## 1. Instalación



## Características- ###  Despliegue en local (Windows)

  * Video de instalación. [Aquí](https://www.youtube.com/watch?v=9Ds2DR3QLGY)

- Facturación electrónica (Invoices)  * [Pasos aquí](https://git.buho.la/facturalatam/co-apidian2025/-/blob/master/Comandos%20Instalacion%20API%202025%20Windows.txt)

- Notas crédito y débito- ### Despliegue en linux

- Documentos soporte  *  Exclusivo para Ubuntu **20.04** | entorno LAMP | ejecucion de comandos de forma manual.

- Documentos equivalentes POS     * [Video aquí](https://www.youtube.com/watch?v=rEgrHADjsCY)

- Nómina electrónica     * [Comandos aquí](https://git.buho.la/facturalatam/co-apidian2025/-/blob/master/Comandos%20Instalacion%20API%202025%20Linux%20Ubuntu%2020.txt?ref_type=heads)

- Validación previa y habilitación DIAN  * Exclusivo para Ubuntu **20.04** | Entorno LAMP | ejecución de script.

- Generación de XML UBL 2.1     * [Pasos y script aquí](https://git.buho.la/facturalatam/co-apidian2025/-/snippets/35)

- Firma digital de documentos  * Linux | Entorno **Docker** | *Aporte de usuario de la comunidad*

- Envío automático a DIAN     * [Descarga aquí](https://git.buho.la/facturalatam/co-apidian2025/-/blob/master/api_docker.zip?ref_type=heads)

- Consulta de estados DIAN- ### Despliegue con Docker

- Generación de PDF personalizable  * Ubuntu 20.04 , 22.04 y 24.04 | sólo API | sin dominio | proceso manual

- Envío de documentos por email     * [Pasos aquí](https://git.buho.la/-/snippets/31)

- API RESTful completa- ### CPanel

- Soporte multi-empresa  * [Ver manual aquí](https://git.buho.la/facturalatam/co-apidian2025/-/snippets/37#requisitos)

- Integración con sector salud (RIPS)

## 2. Actualización

## Tecnologías

- Actualizar repositorio de gitlab de una versión antigua a la actual. [Video aquí](https://www.youtube.com/watch?v=6lwLKQCYvNY)

- **Framework**: Laravel 5.8- Comandos para actualizar repositorio **Pto. 1** y comandos para actualizar **Pto.2**. [Aquí](https://git.buho.la/facturalatam/co-apidian2025/-/blob/master/Proceso%20de%20actualizacion%20APIDIAN.txt?ref_type=heads)

- **Base de datos**: MariaDB 10.5.6

- **Servidor web**: Nginx## 3. Ejemplos para la API / Collection POSTMAN

- **PHP**: 7.2.18 (PHP-FPM)

- **Contenedorización**: Docker + Docker Compose- Descargar Postman [Aquí](https://www.postman.com/downloads/)

- **Firma digital**: XMLSecLibs- Ver ejemplos en sitio web. [Aquí](https://documenter.getpostman.com/view/1431398/2sAY4uCido)

- **Estándar**: UBL 2.1- Descargar colección para importar en Postman (*actualizaciones menos constantes*). [Aquí](https://git.buho.la/facturalatam/co-apidian2025/-/blob/master/ApiDianV2.1.postman_collection.json?ref_type=heads)



## Estructura del Proyecto## 4. Proceso de habilitación DIAN Validación Previa UBL 2.1



```- Ver video explicativo [Aquí](https://www.youtube.com/watch?v=csTmbd1Ere8)

apidian/

├── app/## 5.- Configuración / Habilitación / Producción API

│   ├── Console/           # Comandos Artisan* Ver video explicativo [Aquí](https://www.youtube.com/watch?v=TSF2nHN4W1I)

│   ├── Http/

│   │   ├── Controllers/   # API Controllers## 6.- Cómo pasar a producción

│   │   ├── Middleware/    # Middlewares* Ver video explicativo [Aquí](https://www.youtube.com/watch?v=gBtd4XqwWtg)

│   │   └── Requests/      # Form Requests

│   ├── Models/            # Modelos Eloquent## APP

│   └── Services/          # Servicios de negocio* Aplicación movil en formato web [visitar URL](https://facturalatam.com/app/)

├── config/                # Configuración

├── database/## Extras

│   ├── migrations/        # Migraciones

│   └── seeders/           # Seeders* Para deshabilitar registro de nuevas empresas por API:

├── public/                # Punto de entrada público  * ubicarse en la raiz del proyecto

├── resources/  * editar el archivo .env (*utilizar nano o un editor en consola*)

│   ├── templates/  * añadir si no existe el parametro `ENABLE_API_REGISTER` con el valor en `false`

│   │   ├── xml/          # Templates XML UBL  * eliminar la cache de la aplicacion `php artisan config:cache & php artisan cache:clear`

│   │   └── pdf/          # Templates PDFLos cambios podrán decargarse a partir de enero del 2025 progresivamente, con la nueva documentación respectiva.

│   └── views/             # Vistas Blade* Para añadir un correo para envio de los documentos a los clientes:

├── routes/  * ubicarse en la raiz del proyecto y editar el archivo .env (*utilizar nano o un editor en consola*)

│   ├── api.php           # Rutas API  * ajustar los siguientes valores de acuerdo a su proveedor de correos

│   └── web.php           # Rutas Web  ```

├── storage/  MAIL_DRIVER=smtp

│   ├── app/              # Archivos generados  MAIL_HOST=smtp.gmail.com

│   ├── framework/  MAIL_PORT=465

│   └── logs/             # Logs de aplicación  MAIL_USERNAME=username@gmail.com

├── tests/                # Tests automatizados  MAIL_PASSWORD=password

├── .env                  # Variables de entorno  MAIL_ENCRYPTION=ssl

├── artisan               # CLI Laravel  MAIL_FROM_ADDRESS=username@gmail.com

├── composer.json         # Dependencias PHP  MAIL_FROM_NAME=username

└── docker-compose.yml    # Configuración Docker  ```

```  * eliminar la cache de la aplicacion `php artisan config:clear && php artisan config:cache & php artisan optimize:clear`


## Requisitos del Sistema

- Docker 20.10+
- Docker Compose 1.29+
- Mínimo 2GB RAM
- 10GB espacio en disco
- Puertos disponibles: 80, 443, 8081, 3306
- Certificado digital de firma (formato .p12)

## Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/fullsyssantamarta/api_imperium.git
cd api_imperium
```

### 2. Configurar Variables de Entorno

```bash
cp .env.example .env
nano .env
```

Variables principales:
```env
APP_NAME="API DIAN"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=apidian
DB_USERNAME=root
DB_PASSWORD=tu_password_seguro

# Configuración DIAN
DIAN_URL=https://vpfe.dian.gov.co/WcfDianCustomerServices.svc
DIAN_TESTSET_ID=tu_testset_id
DIAN_SOFTWARE_ID=tu_software_id
DIAN_SOFTWARE_PIN=tu_pin

# Correo electrónico
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=tu_correo@gmail.com
MAIL_FROM_NAME="Sistema Facturación"
```

### 3. Iniciar Contenedores Docker

```bash
docker-compose up -d
```

### 4. Instalar Dependencias

```bash
docker exec php_api composer install
```

### 5. Generar Clave de Aplicación

```bash
docker exec php_api php artisan key:generate
```

### 6. Ejecutar Migraciones

```bash
docker exec php_api php artisan migrate --seed
```

### 7. Configurar Permisos

```bash
docker exec php_api chown -R www-data:www-data storage bootstrap/cache
docker exec php_api chmod -R 775 storage bootstrap/cache
```

### 8. Crear Usuario Administrador

```bash
docker exec php_api php artisan user:create
```

## Uso de la API

### Autenticación

La API utiliza tokens de autenticación:

```bash
# Obtener token
curl -X POST https://api.tu-dominio.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@ejemplo.com",
    "password": "password"
  }'
```

### Endpoints Principales

#### Crear Empresa

```bash
POST /api/companies
Content-Type: application/json
Authorization: Bearer {token}

{
  "identification_number": "900123456",
  "dv": "1",
  "name": "Empresa Ejemplo SAS",
  "municipality_id": "11001",
  "address": "Calle 123 #45-67",
  "phone": "3001234567",
  "email": "contacto@empresa.com"
}
```

#### Generar Factura Electrónica

```bash
POST /api/ubl2.1/invoice
Content-Type: application/json
Authorization: Bearer {token}

{
  "number": "FACT-001",
  "date": "2025-10-21",
  "time": "10:30:00",
  "customer": {
    "identification_number": "123456789",
    "name": "Cliente Ejemplo",
    "email": "cliente@ejemplo.com"
  },
  "items": [
    {
      "code": "PROD-001",
      "description": "Producto ejemplo",
      "quantity": 1,
      "price": 100000,
      "tax_rate": 19
    }
  ],
  "payment_form": "1",
  "payment_method": "10",
  "notes": "Notas adicionales"
}
```

#### Consultar Estado DIAN

```bash
GET /api/invoice/{id}/status
Authorization: Bearer {token}
```

#### Descargar PDF

```bash
GET /api/invoice/{id}/pdf
Authorization: Bearer {token}
```

#### Enviar por Email

```bash
POST /api/invoice/{id}/email
Authorization: Bearer {token}

{
  "email": "cliente@ejemplo.com"
}
```

### Colección Postman

Descarga la colección completa de Postman para probar todos los endpoints:

```bash
/root/datos/ApiDianV2.1.postman_collection.json
```

Importar en Postman para pruebas completas de la API.

## Configuración DIAN

### 1. Obtener Certificado Digital

Obtener certificado de firma digital en formato .p12 de una entidad certificadora autorizada por la DIAN.

### 2. Configurar Certificado en la API

```bash
# Subir certificado a storage
docker cp certificado.p12 php_api:/var/www/html/storage/app/certificates/

# Configurar en empresa
# A través de la interfaz web o API
```

### 3. Proceso de Habilitación DIAN

**Paso 1: Validación Previa**
- Configurar empresa en modo habilitación
- Generar set de pruebas
- Enviar documentos de prueba a DIAN
- Validar respuestas

**Paso 2: Habilitación**
- Completar validación previa exitosamente
- Solicitar habilitación a DIAN
- Obtener software_id y clave técnica
- Configurar en producción

**Paso 3: Producción**
- Cambiar modo a producción
- Configurar software_id de producción
- Comenzar facturación en vivo

### Video Tutorial

Para más detalles, ver videos explicativos en la documentación oficial de APIDIAN.

## Tipos de Documentos Soportados

### Documentos Electrónicos

- **01**: Factura de Venta
- **02**: Factura de Exportación
- **03**: Factura de Contingencia
- **04**: Factura AIU

### Notas

- **91**: Nota Crédito
- **92**: Nota Débito

### Documentos Soporte

- **05**: Documento Soporte en Adquisiciones
- **95**: Nota Ajuste Documento Soporte

### Otros

- **20**: Documento Equivalente POS
- **Nómina**: Nómina Electrónica

## Sector Salud (RIPS)

La API incluye soporte completo para facturación del sector salud con información RIPS:

```json
{
  "health_fields": {
    "provider_code": "COD123",
    "authorization_number": "AUT456",
    "health_type_user_id": 1,
    "health_coverage_id": 2,
    "contract_number": "CNT789",
    "policy_number": "POL012",
    "co_payment": 10000,
    "moderating_fee": 5000
  }
}
```

## Templates Personalizables

### Templates XML

Los templates XML se encuentran en:
```
/resources/templates/xml/
```

Personalizables según necesidades específicas.

### Templates PDF

Los templates PDF se encuentran en:
```
/resources/views/pdfs/
```

Múltiples diseños disponibles:
- template1 (Clásico)
- template2 (Moderno)
- template3 (Minimalista)

## Comandos Útiles

```bash
# Limpiar caché
docker exec php_api php artisan cache:clear
docker exec php_api php artisan config:clear
docker exec php_api php artisan view:clear

# Ver logs
docker exec php_api tail -f storage/logs/laravel.log

# Ejecutar tests
docker exec php_api php artisan test

# Verificar estado de documentos pendientes
docker exec php_api php artisan dian:check-pending

# Reenviar documentos fallidos
docker exec php_api php artisan dian:retry-failed

# Generar reporte de documentos
docker exec php_api php artisan dian:report --date=2025-10-21
```

## Mantenimiento

### Backup

```bash
# Ejecutar backup completo del sistema
/root/backup/backup_imperium.sh

# Configurar backups automáticos
/root/backup/setup_automatic_backups.sh
```

El backup incluye:
- Base de datos apidian
- Código de la aplicación
- Certificados digitales
- Templates personalizados
- Logs y configuraciones

### Actualización

```bash
# Detener servicios
docker-compose down

# Actualizar código
git pull origin master

# Actualizar dependencias
docker-compose up -d
docker exec php_api composer install

# Ejecutar migraciones
docker exec php_api php artisan migrate

# Limpiar caché
docker exec php_api php artisan optimize:clear
```

### Monitoreo

```bash
# Estado de contenedores
docker ps

# Logs en tiempo real
docker-compose logs -f php_api

# Uso de recursos
docker stats

# Verificar espacio en disco
df -h
```

## Solución de Problemas

### Error al Firmar Documentos

```bash
# Verificar certificado
docker exec php_api openssl pkcs12 -info -in storage/app/certificates/certificado.p12

# Verificar permisos
docker exec php_api ls -la storage/app/certificates/
```

### Error de Conexión con DIAN

```bash
# Verificar conectividad
docker exec php_api curl https://vpfe.dian.gov.co

# Verificar logs
docker exec php_api tail -100 storage/logs/dian.log
```

### Error de Base de Datos

```bash
# Verificar conexión
docker exec php_api php artisan migrate:status

# Reiniciar MariaDB
docker restart mariadb
```

### Problemas de Rendimiento

```bash
# Cachear configuración
docker exec php_api php artisan config:cache
docker exec php_api php artisan route:cache

# Optimizar autoload
docker exec php_api composer dump-autoload -o

# Limpiar logs antiguos
docker exec php_api find storage/logs/ -name "*.log" -mtime +30 -delete
```

## Seguridad

### Recomendaciones

1. Usar HTTPS en producción
2. Proteger certificados digitales (.p12)
3. Cambiar contraseñas por defecto
4. Limitar acceso por IP
5. Implementar rate limiting
6. Monitorear logs de acceso
7. Mantener actualizaciones al día
8. Backup regular de certificados

### Permisos de API

La API maneja diferentes niveles de acceso:
- Admin: Acceso completo
- Company Admin: Gestión de su empresa
- User: Generación de documentos
- Readonly: Solo consulta

## Desarrollo

### Entorno de Desarrollo

```bash
# Configurar modo desarrollo
APP_ENV=local
APP_DEBUG=true

# Instalar dependencias dev
docker exec php_api composer install

# Ejecutar tests
docker exec php_api php artisan test

# Generar documentación API
docker exec php_api php artisan l5-swagger:generate
```

### Testing

```bash
# Ejecutar todos los tests
docker exec php_api php artisan test

# Tests específicos
docker exec php_api php artisan test --filter InvoiceTest

# Con coverage
docker exec php_api php artisan test --coverage
```

## Documentación Adicional

- [Documentación de Backups](/root/documentacion/DOCUMENTACION_BACKUPS.md)
- [Documentación Laravel 5.8](https://laravel.com/docs/5.8)
- [Especificaciones UBL 2.1 DIAN](https://www.dian.gov.co)
- [Portal DIAN](https://www.dian.gov.co/facturacion)

## Recursos Externos

- [Postman Collection](https://documenter.getpostman.com/view/1431398/2sAY4uCido)
- [Videos Tutoriales APIDIAN](https://www.youtube.com/@facturalatam)
- [Documentación DIAN Oficial](https://www.dian.gov.co/facturacion)

## Soporte Técnico

### Contacto

**Fullsys Tecnología**
- **Desarrollador**: Fulvio Leonardo Badillo Caseres
- **Email**: fullsyssantamarta@gmail.com
- **Celular**: +57 302 548 0682
- **Ubicación**: Santa Marta, Colombia

### Horario de Soporte

- Lunes a Viernes: 8:00 AM - 6:00 PM (COT)
- Sábados: 9:00 AM - 1:00 PM (COT)
- Emergencias: Disponible por WhatsApp

### Canales de Soporte

1. **Email**: fullsyssantamarta@gmail.com
2. **WhatsApp**: +57 302 548 0682
3. **Issues GitHub**: https://github.com/fullsyssantamarta/api_imperium/issues

### Servicios Disponibles

- Instalación y configuración
- Habilitación DIAN
- Personalización de templates
- Integración con sistemas existentes
- Capacitación de usuarios
- Soporte técnico continuo
- Actualizaciones y mantenimiento

## Licencia

Copyright © 2025 Fullsys Tecnología - Santa Marta, Colombia

Este software está basado en APIDIAN 2025 y ha sido personalizado y adaptado por Fullsys Tecnología para integración con sistemas empresariales.

Todos los derechos reservados. Este software es propiedad de Fullsys Tecnología y está protegido por las leyes de derechos de autor colombianas e internacionales.

## Créditos

**Desarrollo y Mantenimiento:**
- Fullsys Tecnología
- Fulvio Leonardo Badillo Caseres
- Santa Marta, Magdalena, Colombia

**Basado en:**
- APIDIAN 2025 by FacturaLatam

## Agradecimientos

Agradecimientos especiales a:
- FacturaLatam por APIDIAN base
- Comunidad Laravel Colombia
- DIAN por especificaciones técnicas
- Clientes que han confiado en nuestros servicios

---

**Versión**: 2.1  
**Última actualización**: Octubre 2025  
**Estado**: Producción  
**Compatibilidad DIAN**: UBL 2.1 - Resolución 000042/2020
