<?
/**
 * var $category Model\Product\Category\Entity
 * var $prod_cats string // [ 'Малая бытовая техника для кухни',  'Холодильники и морозильники' ]
 * from /main/view/DefaultLayout.php
 **/
?>
<? if (!empty($prod_cats)): ?>
  <div id="sociomanticCategoryPage" data-prod-cats="<?= $prod_cats."\n" ?>" class="jsanalytics"></div>
<? endif; ?>