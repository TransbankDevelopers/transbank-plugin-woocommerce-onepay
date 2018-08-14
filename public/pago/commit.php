<!DOCTYPE html>
<!--[if lt IE 7 ]><html lang="en" class="no-js ie6"><![endif]-->
<!--[if IE 7 ]><html lang="en" class="no-js ie7"><![endif]-->
<!--[if IE 8 ]><html lang="en" class="no-js ie8"><![endif]-->
<!--[if IE 9 ]><html lang="en" class="no-js ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="en" class="no-js"><!--<![endif]-->

<?php
require(dirname(__FILE__, 6) .'/wp-blog-header.php');
    use Transbank\Onepay\Transaction;
    use Transbank\Onepay\OnepayBase;
    global $woocommerce, $order;

    $order_id = WC()->session->get('order_id');

    OnepayBase::setSharedSecret("P4DCPS55QB2QLT56SQH6#W#LV76IAPYX");
    OnepayBase::setApiKey("mUc0GxYGor6X8u-_oB3e-HWJulRG01WoC96-_tUA3Bg");
    $externalUniqueNumber = $_POST['externalUniqueNumber'];
    $transactionCommitResponse = Transaction::commit($_POST['occ'], $externalUniqueNumber);
    $order = new WC_Order($order_id);
    if($transactionCommitResponse->getResponseCode() == 'OK') {
        $order->update_status('completed');
    }
    else {
        $order->update_status('cancelled');
    }

?>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="utf-8">
    <style>
        .onepay-logo-container {
            margin:auto;
            width: 120px;
        }
        .onepay-logo-img {
            width: 120px;
            margin:auto;
        }
        .onepay-success-container {
            width: 50%;
            padding: 20px 30px 20px 30px;
            margin: auto;
            border: 1px solid black;
            text-align:center;
        }

        .transaction-commit-info-title {
            font-weight: bold;
            font-family: Roboto;
            line-height: 1.5rem;
        }
        .return-home-btn-container {
            padding-top: 20px;
            width: 150px;
            margin:auto;
        }
        .return-home-btn {
            height: 2rem;
            background-color: #FFBB15;
            color: white;
            border: 1px solid grey;
            border-radius: 3px;
            font-family: Roboto;
            font-weight: bold;
            text-align: center;
            cursor:pointer;
        }
        .return-home-btn-text {
            vertical-align: middle;
            display: inline-block;
            line-height: 2rem;
            padding: 0 1em 0 1em;
            text-decoration: none;
            color: white;
        }

        .return-home-btn-text:hover {
            color: blue;
        }

    </style>
</head>

<?php
 if($transactionCommitResponse->getResponseCode() == 'OK') { ?>
    <body>
        <div class="onepay-logo-container">
            <img class="onepay-logo-img"
                 src= <?php echo plugin_dir_url( dirname( __FILE__ ) ) . "../public/images/img_onepay.png" ?>>
        </div>

        <div class="onepay-success-container">
            <div>
                <div class="transaction-commit-info-title">OCC:</div>
                <div><?php echo $transactionCommitResponse->getOcc() ?> </div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Número de carro:</div>
                <div><?php echo $externalUniqueNumber ?> </div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Código de autorización:</div>
                <div><?php echo  $transactionCommitResponse->getAuthorizationCode() ?></div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Orden de compra:</div>
                <div><?php echo $transactionCommitResponse->getBuyOrder() ?></div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Descripción:</div>
                <div> <?php echo $transactionCommitResponse->getDescription() ?></div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Monto compra:</div>
                <div><?php echo $transactionCommitResponse->getAmount() ?></div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Numero de cuotas:</div>
                <div><?php echo $transactionCommitResponse->getInstallmentsNumber() ?></div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Monto cuota:</div>
                <div><?php echo $transactionCommitResponse->getInstallmentsAmount() ?></div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Fecha:</div>
                <div> <?php echo $transactionCommitResponse->getIssuedAt() ?> </div>
            </div>
            <div>
                <div class="transaction-commit-info-title">Anulación</div>
                <div>
                    <a href=<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'pago/refund.php' ?>?amount=<?php echo urlencode($transactionCommitResponse->getAmount())?>&occ=<?php echo urlencode($transactionCommitResponse->getOcc()) ?>&externalUniqueNumber=<?php echo urlencode($externalUniqueNumber)?>&authorizationCode=<?php echo urlencode($transactionCommitResponse->getAuthorizationCode())?>
                    >Anular esta compra</a>
                </div>
            </div>
        </div>
        <div class="return-home-btn-container">
            <div class='return-home-btn'>
                <a href="/" class="return-home-btn-text">Volver al inicio</a>
            </div>
        </div>
    </body>
 <?php } else { ?>

     Transacción fallida
 <?php } ?>

</html>
