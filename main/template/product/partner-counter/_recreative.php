<?php
/**
 * @var $page    \View\Product\IndexPage
 * @var $product \Model\Product\Entity
 */
?>

<script type="text/javascript">
    (function(d,w) {
        var n=d.getElementsByTagName("script")[0],
            s=d.createElement('script'),
            f=function()
            {n.parentNode.insertBefore(s, n);}

            ;
        s.type="text/javascript";
        s.async=true;
        s.src='http://track.recreativ.ru/trck.php?shop=45&offer=<?= $product->getId() ?>&rnd='+Math.floor(Math.random()*999);
        if(window.opera=="[object Opera]")
        {d.addEventListener("DOMContentLoaded", f, false);}

        else
        {f();}

    })(document,window);
</script>
