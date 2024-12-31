# **API REST 2025**

## Acerca de FACTURALATAM

Estamos enfocados en crear herramientas y soluciones para desarrolladores de software, empresas medianas y pequeñas, tenemos un sueño, queremos democratizar la facturación electrónica.

## 1.- Proceso de habilitación DIAN Validación Previa UBL 2.1

Obtener parámetros para software directo - [Aquí](https://www.youtube.com/watch?v=csTmbd1Ere8)


## 2.- Ejemplos para la API / Collection POSTMAN

 Primero descargue la herramienta POSTMAN desde internet, y luego importe el archivo collection aquí [postman collection](https://gitlab.buho.la/facturalatam/co-apidian2025/-/blob/master/ApiDianV2.1.postman_collection.json?ref_type=heads "Clic") 


## 3.- Videos para la instalación y despliegue en local (Windows)

* Video de instalación [Aquí](https://www.youtube.com/watch?v=9Ds2DR3QLGY)

* Pasos instalación Linux Ubuntu 22 [Aquí](https://gitlab.buho.la/facturalatam/co-apidian2025/-/blob/master/Comandos%20Instalacion%20API%202024%20Windows.txt?ref_type=heads)

## 4.- Manuales de instalación en VPS LINUX

* Pasos instalación Linux Ubuntu 22 [Aquí](https://gitlab.buho.la/facturalatam/co-apidian2024/-/blob/master/Comandos%20Instalacion%20API%202024%20Linux%20Ubuntu%2020.txt?ref_type=heads)

* Video de instalación [Aquí](https://www.youtube.com/watch?v=rEgrHADjsCY)


## 5.- Configuración / Habilitación / Producción API
* [Guía](https://www.youtube.com/watch?v=TSF2nHN4W1I)


## 6.- Cómo pasar a producción
* [Guía](https://www.youtube.com/watch?v=gBtd4XqwWtg)


## Extras
* Actualizacion desde APIDIAN2022 y la api APIDIAN2023 [Aquí](https://www.youtube.com/watch?v=6lwLKQCYvNY)

* Script de actualización
[Aquí](https://gitlab.buho.la/facturalatam/co-apidian2024/-/blob/master/Proceso%20de%20actualizacion%20APIDIAN.txt?ref_type=heads)

* Imagen docker instalación api
[Aquí](https://gitlab.buho.la/facturalatam/co-apidian2024/-/blob/master/api_docker.zip?ref_type=heads)

* Deshabilitar registro por API
  * añadir si no existe el parametro `ENABLE_API_REGISTER` en el .env con el valor en `false`
  * eliminar la cache de la aplicacion `php artisan config:cache & php artisan cache:clear`
Los cambios podrán decargarse a partir de enero del 2025 progresivamente, con la nueva documentación respectiva.

