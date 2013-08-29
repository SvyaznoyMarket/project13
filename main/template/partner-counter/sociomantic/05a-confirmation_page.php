<?
/**
 * from /main/view/DefaultLayout.php
 * var $smantic Views\Sociomantic
 * var $cart_prods
 **/

$region_id = \App::user()->getRegion()->getId();

$sonarBasket = [];
$sonarBasket['products']    = [];

$i=0;
$orderSum = 0;
$orderNumber = '';
foreach($orders as $order) {
    $orderSum += $order->getPaySum();
    $i++; if ($i>1) $orderNumber .= ', '; // $orders не может быть полноценным массивом и иметь несколько номеров заказов, но всё же
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
$sonarBasket['zzz']    = 'zzz';


/* example:
{ identifier: '461-1177', amount: 4990.00, currency: 'RUB', quantity: 1 },
{ identifier: '452-9682', amount: 23990.00, currency: 'RUB', quantity: 1 }
*/
?>

<div id="sociomanticConfirmationPage" data-sonar-basket="<?= $page->json($sonarBasket) ?>" class="jsanalytics"></div>
