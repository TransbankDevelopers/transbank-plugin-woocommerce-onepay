![Woocommerce](https://woocommerce.com/wp-content/themes/woo/images/logo-woocommerce@2x.png)

#  Woocommerce Docker para desarrollo

### PHP 7.3 + Mysql + Woocommerce  3.9.1

### Requerimientos

**MacOS:**

Instalar [Docker](https://docs.docker.com/docker-for-mac/install/), [Docker-compose](https://docs.docker.com/compose/install/#install-compose) y [Docker-sync](https://github.com/EugenMayer/docker-sync/wiki/docker-sync-on-OSX).

**Windows:**

Instalar [Docker](https://docs.docker.com/docker-for-windows/install/), [Docker-compose](https://docs.docker.com/compose/install/#install-compose) y [Docker-sync](https://github.com/EugenMayer/docker-sync/wiki/docker-sync-on-Windows).

**Linux:**

Instalar [Docker](https://docs.docker.com/engine/installation/linux/docker-ce/ubuntu/) y [Docker-compose](https://docs.docker.com/compose/install/#install-compose).

### Como usar

De forma automática se creará una imagen Wordpress, se instalará WooCommerce con el tema Storefront, se creará un producto de ejemplo y finalmente se activará este plugin.

Para instalar Woocommerce, hacer lo siguiente:

**NOTA:** La primera vez que se ejecuta ./start o ./build demorará en instalar todo, esperar al menos unos 5 minutos.

### Construir el contenedor desde cero

```
cd docker-php7.3
./build
```

### Iniciar el contenedor construido anteriormente

```
./start
```

### Acceder al contenedor

```
./shell
```

### Importante corregir permisos del directorio para instalar el plugin mediante zip

```
./shell root
chown -R www-data:www-data /var/www/html/wp-content
```


### Paneles

**Web server:** http://localhost:8082

**Admin:** http://localhost:8082/wp-admin

    user: admin
    password: admin

## Extras para usar ngrok y probar en dominio virtual especialmente para emular producción

1.- Ejecutar ngrok y obtener la url dada por ngrok en `Forwarding` http

    ngrok http 8082

2.- Modificar el archivo `init.sh` y reconstruir el docker

    --url=URL_DADA_POR_NGROK

    Ej: --url=c0c8db10.ngrok.io

3.- Entrar al docker con `./shell` y agregar estas lineas a `wp-config.php`:

    define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST']);
    define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);

