<?
/**
 * from /main/view/DefaultLayout.php
 * var $smantic Views\Sociomantic
 **/

$region_id = \App::user()->getRegion()->getId();

?>
<script type="text/javascript">
    var sonar_basket = {
        products: [
            <?
            $i=0;
            $orderSum = 0;
            $orderNumber = '';
            foreach($orders as $order) :
                $orderSum += $order->getPaySum();
                $i++; if ($i>1) $orderNumber .= ', '; // $orders не может быть полноценным массивом и иметь несколько номеров заказов, но всё же
                $orderNumber .= $order->getNumber();
                foreach ($order->getProduct() as $prod) {
                    echo "{ identifier: '" . $smantic->resetProductId( $prod ) . "', amount: ".$prod->getPrice().", currency: 'RUB', quantity: ".$prod->getQuantity()." }";
                    /* example:
                    { identifier: '461-1177', amount: 4990.00, currency: 'RUB', quantity: 1 },
                    { identifier: '452-9682', amount: 23990.00, currency: 'RUB', quantity: 1 }
                    */
                }
            endforeach;
            $orderNumber = " '$orderNumber' ";
            ?>
        ],
        transaction: <?= $orderNumber ?>,
        amount: <?= $orderSum; ?>,
        currency: 'RUB'
    };

</script>