<?php

use Transbank\Onepay\Refund;
use Transbank\Onepay\OnepayBase;
use Transbank\Onepay\Exceptions\RefundCreateException;

require(dirname(__FILE__, 6) .'/wp-blog-header.php');
global $woocommerce, $order;


OnepayBase::setSharedSecret("P4DCPS55QB2QLT56SQH6#W#LV76IAPYX");
OnepayBase::setApiKey("mUc0GxYGor6X8u-_oB3e-HWJulRG01WoC96-_tUA3Bg");

function refund($order_id, $refund_reason = 'Customer requested refund') {


    $order = new WC_Order(WC()->session->get('order_id'));
// If it's something else such as a WC_Order_Refund, we don't want that.
    if( ! is_a( $order, 'WC_Order') ) {
        return new WP_Error( 'wc-order', __( 'Provided ID is not a WC Order') );
    }

    try {
        $refundResponse = Refund::create($_GET['amount'], $_GET['occ'], $_GET['externalUniqueNumber'], $_GET['authorizationCode']);

        if($refundResponse->getResponseCode() == 'OK') {
            echo "Success!";


            // Get Items
            $order_items   = $order->get_items();
            if( 'refunded' == $order->get_status() ) {
                return new WP_Error( 'wc-order', __( 'Order has been already refunded' ) );
            }
            // Refund Amount
            $refund_amount = 0;
            // Prepare line items which we are refunding
            $line_items = array();
            if ( $order_items = $order->get_items()) {
                foreach( $order_items as $item_id => $item ) {
                    $item_meta 	= $order->get_item_meta( $item_id );

                    $product_data = wc_get_product( $item_meta["_product_id"][0] );

                    $item_ids[] = $item_id;
                    $tax_data = $item_meta['_line_tax_data'];
                    $refund_tax = 0;
                    if( is_array( $tax_data[0] ) ) {
                        $refund_tax = array_map( 'wc_format_decimal', $tax_data[0] );
                    }
                    $refund_amount = wc_format_decimal( $refund_amount ) + wc_format_decimal( $item_meta['_line_total'][0] );
                    $line_items[ $item_id ] = array( 'qty' => $item_meta['_qty'][0], 'refund_total' => wc_format_decimal( $item_meta['_line_total'][0] ), 'refund_tax' =>  $refund_tax );

                }
            }

            $refund = wc_create_refund( array(
                'amount'         => $refund_amount,
                'reason'         => $refund_reason,
                'order_id'       => $order_id,
                'line_items'     => $line_items,
                'refund_payment' => false # False because we do the refund manually on Transbank
            ) );
            return $refund;
        }
    } catch (RefundCreateException $exception) {
        echo "Already refunded on Transbank";
    }
}






echo "jajajaj soy php\n";

echo "amount = " . $_GET['amount'] . "\n";
echo "occ = " . $_GET['occ'] . "\n";
echo "EUN = " . $_GET['externalUniqueNumber'] . "\n";
echo "Auth code = " . $_GET['authorizationCode'] . "\n";
echo "ORDER ID = " . WC()->session->get('order_id') . "\n";

var_dump(  refund(WC()->session->get('order_id')) );
