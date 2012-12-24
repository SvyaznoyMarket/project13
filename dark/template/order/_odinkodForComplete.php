<?php
/**
 * @var $user   \Session\User
 * @var $orders \Model\Order\Entity[]
 */
?>

<? if ($cusId = \Analytics\Odinkod::getCusId($user->getRegion())): ?>
    <? foreach ($orders as $order): ?>
        <script language="javascript">
            var odinkod = {
                "type": "transaction",
                "order_value":"<?= $order->getSum() ?>",
                "transaction_id":"<?= $order->getNumber() ?>",
                "product_list":"<?= implode(',', array_map(function ($orderProduct) { /** @var $orderProduct \Model\Order\Product\Entity */ return $orderProduct->getId(); }, $order->getProduct())) ?>"
            };
            document.write('<scr'+'ipt src="'+('https:' == document.location.protocol ? 'https://ssl.' : 'http://') +
                    'cdn.odinkod.ru/tags/<?= $cusId ?>.js"></scr'+'ipt>');
        </script>
    <? endforeach ?>
<? endif ?>