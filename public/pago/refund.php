<?php
//require(dirname(__FILE__, 6) .'/wp-blog-header.php');
//if ( ! defined( 'ABSPATH' ) ) {
//    exit;
//}
//use Transbank\Onepay\Refund;
//use Transbank\Onepay\OnepayBase;
//use Transbank\Onepay\Exceptions\RefundCreateException;
//
//
//global $woocommerce, $order;
//
//OnepayBase::setSharedSecret($this->get_option('shared_secret'));
//OnepayBase::setApiKey($this->get_option( 'apikey' ));
//
//function refund($order_id, $refund_reason = 'Customer requested refund') {
//
//
//    $order = new WC_Order(WC()->session->get('order_id'));
//// If it's something else such as a WC_Order_Refund, we don't want that.
//    if( ! is_a( $order, 'WC_Order') ) {
//        return new WP_Error( 'wc-order', __( 'Provided ID is not a WC Order') );
//    }
//
//    try {
//        $refundResponse = Refund::create($_GET['amount'], $_GET['occ'], $_GET['externalUniqueNumber'], $_GET['authorizationCode']);
//
//        if($refundResponse->getResponseCode() == 'OK') {
//
//
//
//            // Get Items
//            $order_items   = $order->get_items();
//            if( 'refunded' == $order->get_status() ) {
//                return new WP_Error( 'wc-order', __( 'Order has been already refunded' ) );
//            }
//            // Refund Amount
//            $refund_amount = 0;
//            // Prepare line items which we are refunding
//            $line_items = array();
//            if ( $order_items = $order->get_items()) {
//                foreach( $order_items as $item_id => $item ) {
//                    $item_meta 	= $order->get_item_meta( $item_id );
//
//                    $product_data = wc_get_product( $item_meta["_product_id"][0] );
//
//                    $item_ids[] = $item_id;
//                    $tax_data = $item_meta['_line_tax_data'];
//                    $refund_tax = 0;
//                    if( is_array( $tax_data[0] ) ) {
//                        $refund_tax = array_map( 'wc_format_decimal', $tax_data[0] );
//                    }
//                    $refund_amount = wc_format_decimal( $refund_amount ) + wc_format_decimal( $item_meta['_line_total'][0] );
//                    $line_items[ $item_id ] = array( 'qty' => $item_meta['_qty'][0], 'refund_total' => wc_format_decimal( $item_meta['_line_total'][0] ), 'refund_tax' =>  $refund_tax );
//
//                }
//            }
//
//            wc_create_refund( array(
//                'amount'         => $refund_amount,
//                'reason'         => $refund_reason,
//                'order_id'       => $order_id,
//                'line_items'     => $line_items,
//                'refund_payment' => false # False because we do the refund manually on Transbank using the Onepay SDK
//            ) );
//
//            return "
//
//              <h3 class=\"text-center\">Tu transacción fue reversada exitosamente.</h3>
//              <h4 class=\"text-center\">Detalle de transacción: </h4>
//
//            ";
//        }
//    } catch (RefundCreateException $exception) {
//        return "
//              <h3 class=\"text-center\">Tu transacción ya ha sido reversada o no se pudo reversar por un error del servicio.</h3>
//              <h4 class=\"text-center\">Detalle de transacción </h4>
//            ";
//    }
//}
//
//
//$transactionResult = refund(WC()->session->get('order_id'));
//?>
<!---->
<!--<html>-->
<!--<head>-->
<!--    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">-->
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
<!--    <style>-->
<!--        .onepay-logo-container {-->
<!--            margin:auto;-->
<!--            width: 120px;-->
<!--        }-->
<!--        .onepay-logo-img {-->
<!--            width: 120px;-->
<!--            margin:auto;-->
<!--        }-->
<!---->
<!--    </style>-->
<!--</head>-->
<!--<body>-->
<!--<div class="onepay-logo-container">-->
<!--    <img class="onepay-logo-img"-->
<!--         src= --><?php //echo plugin_dir_url( dirname( __FILE__ ) ) . "../public/images/img_onepay.png" ?><!-->-->
<!--</div>-->
<!--<div class="container border">-->
<!--    --><?php //echo $transactionResult ?>
<!--      <div class="row">-->
<!--        <div class="col-md">-->
<!--            <table class="table">-->
<!--                <thead>-->
<!--                    <tr>-->
<!--                    <th scope="col">Producto</th>-->
<!--                    <th scope="col"></th>-->
<!--                    <th scope="col">Precio</th>-->
<!--                    <th scope="col">Cantidad</th>-->
<!--                    <th scope="col">Total</th>-->
<!--                    </tr>-->
<!--                </thead>-->
<!--                <tbody>-->
<!---->
<!--                --><?php
//                foreach ( WC()->cart->get_cart() as $cart_item ) {
//                    $nombre = $cart_item['data']->get_title();
//                    $cantidad = intval($cart_item['quantity']);
//                    $precio = intval($cart_item['data']->get_price());
//                    $imagen = $cart_item['data']->get_image('woocommerce_gallery_thumbnail');
//
//                    echo '<tr>';
//                    echo '<td class="align-middle">'.$imagen.'</td>';
//                    echo '<td class="align-middle">'.$nombre.'</td>';
//                    echo '<td class="align-middle">$'.$precio.'</td>';
//                    echo '<td class="align-middle">'.$cantidad.'</td>';
//                    echo '<td class="align-middle" >$'.($precio*$cantidad).'</td>';
//                    echo '</tr>';
//                }
//                ?>
<!---->
<!--                </tbody>-->
<!--            </table>-->
<!--        </div>-->
<!--      </body>-->
<!--</html>-->
