<?php
/**
 * @var $productCategoryList ProductCategoryEntity[]
 * @var $productList
 */
?>
<?php slot('title', 'Вау-товары специально для вас') ?>

<?php slot('navigation', get_component('default', 'navigation', array('list' => array(array('name' => 'Вау-товары'))))) ?>

<?php slot('page_head', get_partial('product_/page_head')) ?>

<?php slot('left_column') ?>
<?php $limit = 8 ?>
<dl class="bCtg border-none">
  <dt class="bCtg__eOrange">Похожие товары можно
    найти <?php echo count($productCategoryList) > 1 ? 'в категориях' : 'в категории' ?>:
  </dt>
  <dd>
    <ul>
      <?php foreach ($productCategoryList as $productCategory): ?>
      <li class="bCtg__eL2">
        <a href="<?php echo $productCategory->getLink() ?>"><span><?php echo $productCategory->getName() ?></span></a>
      </li>
      <?php endforeach ?>
    </ul>
  </dd>
</dl>
<?php end_slot() ?>

<?php
render_partial('product_/templates/_list_expanded_.php', array(
  'view' => 'expanded',
  'list' => $productList,
))
?>