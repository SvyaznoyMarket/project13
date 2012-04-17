<?php slot('title', 'Вау-товары специально для вас') ?>

<?php slot('navigation', get_component('default', 'navigation', array('list' => array(array('name' => 'Вау-товары'))))) ?>

<?php slot('page_head', get_partial('productSoa/page_head')) ?>

<?php slot('left_column') ?>
<?php $limit = 8 ?>
<dl class="bCtg border-none">
  <dt class="bCtg__eOrange">Похожие товары можно
    найти <?php echo $productCategoryList->count() > 1 ? 'в категориях' : 'в категории' ?>:
  </dt>
  <dd>
    <ul>
      <?php $i = 0; foreach ($productCategoryList as $productCategory): $i++ ?>
      <li class="bCtg__eL2">
        <a
          href="<?php echo url_for('productCatalog_category', $productCategory) ?>"><span><?php echo $productCategory->name ?></span></a>
      </li>
      <?php endforeach ?>
    </ul>
  </dd>
</dl>
<?php end_slot() ?>

<?php include_component('productSoa', 'list', array('view' => 'expanded', 'list' => $productList)) ?>