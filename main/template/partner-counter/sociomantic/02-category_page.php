<?
/**
 * @var $prod_cats Array
 **/
?>
<? if (!empty($prod_cats)): ?>
  <div id="sociomanticCategoryPage" data-prod-cats="<?= $page->json($prod_cats) ?>" class="jsanalytics"></div>
<? endif; ?>