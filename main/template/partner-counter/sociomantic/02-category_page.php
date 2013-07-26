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
        category : <?= $prod_cats."\n" ?>
    };
</script>
<? endif; ?>