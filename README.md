# **APIDIAN 2025**

## Acerca de FACTURALATAM

Estamos enfocados en crear herramientas y soluciones para desarrolladores de software, empresas medianas y pequeñas, tenemos un sueño, queremos democratizar la facturación electrónica.

## 1. Instalación

- ###  Despliegue en local (Windows)
  * Video de instalación. [Aquí](https://www.youtube.com/watch?v=9Ds2DR3QLGY)
  * [Pasos aquí](https://gitlab.buho.la/facturalatam/co-apidian2025/-/blob/master/Comandos%20Instalacion%20API%202024%20Windows.txt)
- ### Despliegue en linux
  *  Exclusivo para Ubuntu **20.04** | entorno LAMP | ejecucion de comandos de forma manual.
     * [Video aquí](https://www.youtube.com/watch?v=rEgrHADjsCY)
     * [Comandos aquí](https://gitlab.buho.la/facturalatam/co-apidian2025/-/blob/master/Comandos%20Instalacion%20API%202024%20Linux%20Ubuntu%2020.txt?ref_type=heads)
  * Exclusivo para Ubuntu **20.04** | Entorno LAMP | ejecución de script.
     * [Pasos y script aquí](https://gitlab.buho.la/-/snippets/29)
  * Linux | Entorno **Docker** | *Aporte de usuario de la comunidad*
     * [Descarga aquí](https://gitlab.buho.la/facturalatam/co-apidian2025/-/blob/master/api_docker.zip?ref_type=heads)

- ### Despliegue con Docker
  * Ubuntu 20.04 y 22.04 | sólo API | sin dominio | proceso manual
     * [Pasos aquí](https://gitlab.buho.la/-/snippets/31)

## 2. Actualización

- Actualizar repositorio de gitlab de una versión antigua a la actual. [Video aquí](https://www.youtube.com/watch?v=6lwLKQCYvNY)
- Comandos para actualizar repositorio **Pto. 1** y comandos para actualizar **Pto.2**. [Aquí](https://gitlab.buho.la/facturalatam/co-apidian2025/-/blob/master/Proceso%20de%20actualizacion%20APIDIAN.txt?ref_type=heads)

## 3. Ejemplos para la API / Collection POSTMAN

- Descargar Postman [Aquí](https://www.postman.com/downloads/)
- Ver ejemplos en sitio web. [Aquí](https://documenter.getpostman.com/view/1431398/2sAY4uCido)
- Descargar colección para importar en Postman (*actualizaciones menos constantes*). [Aquí](https://gitlab.buho.la/facturalatam/co-apidian2025/-/blob/master/ApiDianV2.1.postman_collection.json?ref_type=heads)

## 4. Proceso de habilitación DIAN Validación Previa UBL 2.1

- Ver video explicativo [Aquí](https://www.youtube.com/watch?v=csTmbd1Ere8)

## 5.- Configuración / Habilitación / Producción API
* Ver video explicativo [Aquí](https://www.youtube.com/watch?v=TSF2nHN4W1I)


## 6.- Cómo pasar a producción
* Ver video explicativo [Aquí](https://www.youtube.com/watch?v=gBtd4XqwWtg)


## Extras

* Para deshabilitar registro de nuevas empresas por API:
  * ubicarse en la raiz del proyecto
  * editar el archivo .env (*utilizar nano o un editor en consola*)
  * añadir si no existe el parametro `ENABLE_API_REGISTER` con el valor en `false`
  * eliminar la cache de la aplicacion `php artisan config:cache & php artisan cache:clear`
Los cambios podrán decargarse a partir de enero del 2025 progresivamente, con la nueva documentación respectiva.
* Para añadir un correo para envio de los documentos a los clientes:
  * ubicarse en la raiz del proyecto y editar el archivo .env (*utilizar nano o un editor en consola*)
  * ajustar los siguientes valores de acuerdo a su proveedor de correos
  ```
  MAIL_DRIVER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=465
  MAIL_USERNAME=username@gmail.com
  MAIL_PASSWORD=password
  MAIL_ENCRYPTION=ssl
  MAIL_FROM_ADDRESS=username@gmail.com
  MAIL_FROM_NAME=username
  ```
  * eliminar la cache de la aplicacion `php artisan config:clear && php artisan config:cache & php artisan optimize:clear`
