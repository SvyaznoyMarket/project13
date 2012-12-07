<?php
/**
 * @var $user    \Session\User
 * @var $order   \Model\Order\Entity
 */
?>

<?
$productIds = array();
foreach ($order->getProduct() as $orderProduct) {
    $productIds[] = $orderProduct->getId();
}
?>

<? if (\App::config()->analytics['enabled'] && (bool)$productIds && ($cusId = \Analytics\Odinkod::getCusId($user->getRegion()))): ?>
<script language="javascript">
    var odinkod = {
        "type": "basket",
        "product_list":"<?= implode(',', $productIds) ?>"
    };
    document.write('<scr'+'ipt src="'+('https:' == document.location.protocol ? 'https://ssl.' : 'http://') +
            'cdn.odinkod.ru/tags/<?= $cusId ?>.js"></scr'+'ipt>');
</script>
<? endif ?>