<?php
/**
 * @var $page   \View\Order\CreatePage
 * @var $user   \Session\User
 * @var $orders \Model\Order\Entity[]
 */
?>

<?
$productIds = [];

foreach ($orders as $order) {
    foreach ($order->getProduct() as $orderProduct) {
        $productIds[] = $orderProduct->getId();
    }
}
?>

<script type="text/javascript">
    (function(d,w){
        var n=d.getElementsByTagName("script")[0],
            s=d.createElement("script"),
            f=function()
            {n.parentNode.insertBefore(s,n);}

            ;
        s.type="text/javascript";
        s.async=true;
        s.src="http://track.recreativ.ru/trck.php?shop=45&del=1&offer=<?= implode(',', $productIds) ?>&rnd="+Math.floor(Math.random()*999);
        if(window.opera=="[object Opera]")
        {d.addEventListener("DOMContentLoaded", f, false);}

        else
        {f();}

    })(document,window);
</script>
