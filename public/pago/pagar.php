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
            Aquí debería aparecer el QR:
            <div id="qr"></div>
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
        })(false, document, "https://cdn.rawgit.com/TransbankDevelopers/transbank-sdk-js-onepay/v1.1.0/lib/onepay.min.js", "script",
            window, function () {
                console.log("Onepay JS library successfully loaded.");

                $.ajax({
                    type: "GET",
                    url: "transaction.php",
                    async: true,
                    success: function(data) {
                        // convert json to object
                        console.log(data)
                        var transaction = JSON.parse(data);
                        transaction["paymentStatusHandler"] = {
                            ottAssigned: function () {
                                // callback transacción asinada
                                console.log("Transacción asignada.");

                            },
                            authorized: function (occ, externalUniqueNumber) {
                                // callback transacción autorizada
                                console.log("occ : " + occ);
                                console.log("externalUniqueNumber : " + externalUniqueNumber);
                                var params = {
                                    occ: occ,
                                    externalUniqueNumber: externalUniqueNumber
                                };
                                sendPostRedirect("commit.php", params);

                            },
                            canceled: function () {
                                // callback rejected by user
                                console.log("transacción cancelada por el usuario");
                                onepay.drawQrImage("qr");
                            },
                            authorizationError: function () {
                                // cacllback authorization error
                                console.log("error de autorizacion");
                            },
                            unknown: function () {
                                // callback to any unknown status recived
                                console.log("estado desconocido");
                            }
                        };
                        var onepay = new Onepay(transaction);
                        onepay.drawQrImage("qr");
                    },
                    error: function (data) {
                        console.log("something is going wrong");
                    }
                });

            });


        function sendPostRedirect (destination, params) {
            var form = document.createElement("form");
            form.method = "POST";
            form.action = destination;

            Object.keys(params).forEach(function (key) {
                var param = document.createElement("input");
                param.type = "hidden";
                param.name = key;
                param.value = params[key];
                form.appendChild(param);
            });

            var submit = document.createElement("input");
            submit.type = "submit";
            submit.name = "submitButton";
            submit.style.display = "none";

            form.appendChild(submit);
            document.body.appendChild(form);
            form.submit();
        };


        </script>


        </div>
    </body>
</html>
