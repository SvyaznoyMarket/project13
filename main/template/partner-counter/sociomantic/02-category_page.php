<?
/**
 * var $category Model\Product\Category\Entity
 * var $prod_cats string // [ 'Малая бытовая техника для кухни',  'Холодильники и морозильники' ]
 * from /main/view/DefaultLayout.php
 **/
?>
<? if (!empty($prod_cats)): ?>
<script type="text/javascript">
    var sonar_product = {
        category : <?= $prod_cats ?>
    };
</script>
<? endif; ?>

<script type="text/javascript">
    (function(){
        var s   = document.createElement('script');
        var x   = document.getElementsByTagName('script')[0];
        s.type  = 'text/javascript';
        s.async = true;
        s.src   = ('https:'==document.location.protocol?'https://':'http://')
                + 'eu-sonar.sociomantic.com/js/2010-07-01/adpan/enter-ru';
        x.parentNode.insertBefore( s, x );
    })();
</script>