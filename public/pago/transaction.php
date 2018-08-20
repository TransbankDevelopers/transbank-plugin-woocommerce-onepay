<?php
require(dirname(__FILE__, 6) .'/wp-blog-header.php');
require(dirname(__FILE__, 6) .'/wp-load.php');
use Transbank\Onepay\ShoppingCart;
use Transbank\Onepay\Item;
use Transbank\Onepay\Transaction;
use Transbank\Onepay\OnepayBase;

global $woocommerce, $post;
//$order = new WC_Order($post->ID);

OnepayBase::setSharedSecret("P4DCPS55QB2QLT56SQH6#W#LV76IAPYX");
OnepayBase::setApiKey("mUc0GxYGor6X8u-_oB3e-HWJulRG01WoC96-_tUA3Bg");

$carro = new ShoppingCart();

foreach ( WC()->cart->get_cart() as $cart_item ) {
    $nombre = $cart_item['data']->get_title();
    $cantidad = $cart_item['quantity'];
    $precio = intval($cart_item['data']->get_price());

    $item = new Item($nombre, $cantidad, $precio);
    $carro->add($item);
}

$transaction = Transaction::create($carro);
echo json_encode($transaction);

 ?>


