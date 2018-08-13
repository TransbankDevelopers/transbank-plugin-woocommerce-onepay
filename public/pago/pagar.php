<?php
require(dirname(__FILE__, 6) .'/wp-blog-header.php');
require(dirname(__FILE__, 6) .'/wp-load.php');

use Transbank\Onepay\ShoppingCart;
use Transbank\Onepay\Item;
use Transbank\Onepay\Transaction;
use Transbank\Onepay\OnepayBase;

global $woocommerce, $post, $order;

var_dump($order);
echo "order id is " . WC()->session->get('order_id');

//echo WC()->cart->

 ?>

<html>
    <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
    <body>

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
        Aquí debería aparecer el QR:
        <div id="qr"></div>
    </body>
</html>
