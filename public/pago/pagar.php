<?php

require(dirname(__FILE__, 6) .'/wp-blog-header.php');
require(dirname(__FILE__, 6) .'/wp-load.php');
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $woocommerce, $post, $order;

 ?>

<html>
    <head>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
    <body>

    <div class="container">

      <div class="row">
        <div class="col-md">
        Listado de productos
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">Producto</th>
                    <th scope="col"></th>
                    <th scope="col">Precio</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>


                <?php
                foreach ( WC()->cart->get_cart() as $cart_item ) {

                    $nombre = $cart_item['data']->get_title();
                    $cantidad = intval($cart_item['quantity']);
                    $precio = intval($cart_item['data']->get_price());
                    $imagen = $cart_item['data']->get_image('woocommerce_gallery_thumbnail');

                    echo '<tr>';
                    echo '<td class="align-middle">'.$imagen.'</td>';
                    echo '<td class="align-middle">'.$nombre.'</td>';
                    echo '<td class="align-middle">$'.$precio.'</td>';
                    echo '<td class="align-middle">'.$cantidad.'</td>';
                    echo '<td class="align-middle" >$'.($precio*$cantidad).'</td>';
                    echo '</tr>';
                }
                ?>

                </tbody>
            </table>
        </div>
        <div class="col-sm">
            Total Compra

            Total: <?php echo WC()->cart->get_cart_total(); ?>

            <div onclick="transactionCreate()" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
                        <img src="images/icons/logo_onepay_white.png"> &nbsp; Pagar con OnePay
                    </div>
        </div>

      </div>
        <script>
        (function (o, n, e, p, a, y) {
            var s = n.createElement(p);
            s.type = "text/javascript";
            s.src = e;
            s.onload = s.onreadystatechange = function () {
                if (!o && (!s.readyState
                    || s.readyState === "loaded")) {
                    y();
                }
            };
            var t = n.getElementsByTagName("script")[0];
            p = t.parentNode;
            p.insertBefore(s, t);
        })(false, document, "https://cdn.rawgit.com/TransbankDevelopers/transbank-sdk-js-onepay/v1.2.0/lib/onepay.min.js",
            "script",window, function () {
                var options = {
                    endpoint: './transaction.php',
                    <?php
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                        if ($image != null) {
                            echo 'commerceLogo: "'.$image[0].'",';
                        }
                    ?>
                    callbackUrl: './commit.php'
                    };
                Onepay.checkout(options);

            });

            function transactionCreate() {
                var options = {
                    endpoint: './transaction.php',
                    <?php
                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                        $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                        if ($image != null) {
                            echo 'commerceLogo: "'.$image[0].'",';
                        }
                    ?>
                    callbackUrl: './commit.php'
                    };
                Onepay.checkout(options);
            }

        </script>


        </div>
    </body>
</html>
