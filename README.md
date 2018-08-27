# Transbank WooCommerce Onepay Plugin

Plugin oficial de Woocommerce para Onepay

## Descripción

Este plugin de WooCommerce implementa el [SDK PHP de Onepay](https://github.com/TransbankDevelopers/transbank-sdk-php) en modalidad Checkout. 

## Instalación
El manual de instalación para el usuario final se encuentra disponible [acá](docs/INSTALLATION.md).

## Requisitos 
* PHP 5.6 o superior
* Wordpress
* WooCommerce 3.2 o superior

## Dependencias

El plugin depende de las siguientes librerías:

* transbank/transbank-sdk
* setasign/fpdf
* apache/log4php

Para cumplir estas dependencias, debes instalar [Composer](https://getcomposer.org), e instalarlas con el comando `composer install`.

## Desarrollo

Para apoyar el levantamiento rápido de un ambiente de desarrollo, hemos creado la especificación de contenedores a través de Docker Compose.

Para usarlo, debes tener Docker y Docker Compose instalado en tu máquina, para luego ejecutar:

```bash
docker-compose up
```

De forma automática se creará una imagen Wordpress, se instalará WooCommerce con el tema Storefront, se creará un producto de ejemplo y finalmente se activará este plugin. Para acceder debes dirigir tu navegador hacia `http://localhost:8080`.


Los datos de acceso a la administración son los siguientes:

* Url: http://localhost:8080/wp-admin
* Usuario: onepay
* Contraseña: onepay
