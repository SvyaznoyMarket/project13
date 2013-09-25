<?php
/**
 * @var $page    \View\Product\IndexPage
 * @var $product \Model\Product\Entity
 */
?>

<script id="xcntmyAsync" type="text/javascript">
    /*<![CDATA[*/
    // стр. товара
    var xcnt_product_id = '<?= $product->getId() ?>'; // где ХХ – это ID товара в каталоге рекламодателя.
    /*]]>*/
    (function(){
        var xscr = document.createElement( 'script' );
        var xcntr = escape(document.referrer); xscr.async = true;
        xscr.src = ( document.location.protocol === 'https:' ? 'https:' : 'http:' )
        + '//x.cnt.my/async/track/?r=' + Math.random();
        var x = document.getElementById( 'xcntmyAsync' );
        x.parentNode.insertBefore( xscr, x );
    }());
</script>
