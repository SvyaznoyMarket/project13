<?php
/**
 * @var $user    \Session\User
 * @var $product \Model\Product\Entity
 */
?>

<? if (isset($_GET['ff']) && \App::config()->analytics['enabled'] && ($cusId = \Partner\Counter\Etargeting::getCusId($user->getRegion()))): // TODO: временно закрыто из-за неполадок с maybe3d?>
<script language="javascript">
    var odinkod = {
        "type": "product",
        "product_id":"<?= $product->getId() ?>"
    };
    document.write('<scr'+'ipt src="'+('https:' == document.location.protocol ? 'https://ssl.' : 'http://') +
        'cdn.odinkod.ru/tags/<?= $cusId ?>.js"></scr'+'ipt>');
</script>
<? endif ?>