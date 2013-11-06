<?
/**
 * from /main/view/DefaultLayout.php
 * var $smantic Views\Sociomantic
 * var $cart_prods
 **/
/*
$region_id = \App::user()->getRegion()->getId();

$sonarBasket = [];
$sonarBasket['products']    = [];

$i=0;
$orderSum = 0;
$orderNumber = '';
foreach($orders as $order) {
    $orderSum += $order->getPaySum();
    $i++; if ($i>1) $orderNumber .= ', ';
    $orderNumber .= $order->getNumber();
    foreach ($order->getProduct() as $prod) {
        $sonarBasket['products'][] = (object)[
            'identifier' => $smantic->resetProductId( $prod ),
            'amount'     => $prod->getPrice(),
            'currency'   => 'RUB',
            'quantity'   => $prod->getQuantity(),
        ];
    }
}

$sonarBasket['transaction'] = $orderNumber;
$sonarBasket['amount']      = $orderSum;
$sonarBasket['currency']    = 'RUB';

?>
<div id="sociomanticConfirmationPage" data-sonar-basket="<?= $page->json($sonarBasket) ?>" class="jsanalytics"></div>

<? */
