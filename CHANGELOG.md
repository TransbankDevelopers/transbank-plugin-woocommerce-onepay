# Changelog
Todos los cambios notables a este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
y este proyecto adhiere a [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.0.10] - 2018-12-06
### Fixed
- Corrige compatibilidad con la app de Onepay cuando se compra mediante un dispositivo móvil.
### Added
- Agrega uso de `transactionDescription` cuando el carro tiene un item

## [1.0.9] - 2018-11-28
### Fixed
- Corrige visualización errónea del botón de instalación de Onepay desde el App Store, que impedía que los usuarios pudieran descargar la aplicación si no la tenían instalada

## [1.0.8] - 2018-11-15
### Changed
- Mejora el comportamiento para usuarios iOS que no poseen la aplicación Onepay instalada

## [1.0.7] - 2018-10-29
### Changed
- Corrige un problema de comunicación entre la ventana de pago de Onepay y el servicio de pago de Onepay
- Corrige un problema al abrir la aplicación instalada de Onepay desde el browser de Android.

## [1.0.6] - 2018-09-27
### Changed
- Actualiza SDK JS de Onepay a la versión 1.5.3

## [1.0.5] - 2018-09-14
### Changed
- Actualiza SDK JS de Onepay a la versión 1.5.2

## [1.0.4] - 2018-09-13
### Changed
- Actualiza SDK JS de Onepay a la versión 1.5.1
- Actualiza SDK PHP de Onepay a la versión 1.3.1
- Agrega AppKey de WooCommerce en ambiente de Producción

## [1.0.3] - 2018-09-05
### Changed
- Corrige bug que no permitía descargar el PDF de diagnóstico desde Mozilla Firefox.
- Actualiza SDK JS de Onepay a la versión 1.4.3, para mejorar la forma como se despliega el modal de pago.
