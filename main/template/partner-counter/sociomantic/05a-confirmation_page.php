<?
/**
 * from /main/view/DefaultLayout.php
 **/
?>
<script type="text/javascript">

    var basket = {
        products: [
            <?
            $i=0;
            $orderSum = 0;
            $orderNumber = '';
            foreach($orders as $order) :
                $orderSum += $order->getPaySum();
                $i++; if ($i>1) $orderNumber .= ', '; // $orders не может быть полноценным массивом и иметь несколько номеров заказов, но всё же
                $orderNumber .= $order->getNumber();
                foreach ($order->getProduct() as $orderProduct) {
                    echo "{ identifier: '".$orderProduct->getId()."', amount: ".$orderProduct->getPrice().", currency: 'RUB', quantity: ".$orderProduct->getQuantity()." }";
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

<script type="text/javascript">
    (function () {
        var s = document.createElement('script');
        var x = document.getElementsByTagName('script')[0];
        s.type = 'text/javascript';
        s.async = true;
        s.src = ('https:' == document.location.protocol ? 'https://' : 'http://')
            + 'eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru';
        x.parentNode.insertBefore(s, x);
    })();
</script>