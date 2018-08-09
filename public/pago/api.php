<?php

require(dirname(__FILE__, 6) .'/wp-blog-header.php');

use Transbank\Onepay\ShoppingCart;
use Transbank\Onepay\Item;
use Transbank\Onepay\Transaction;

$carro = new ShoppingCart();

# description, quantity, amount;
$objeto = new Item('Pelota de futbol', 1, 20000);
$carro->add($objeto);

$transaction = Transaction::create($carro);
echo json_encode($transaction);

 ?>


