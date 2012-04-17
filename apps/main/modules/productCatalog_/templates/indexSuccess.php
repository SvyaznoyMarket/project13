<?php
/**
 * @var $productCategoryList ProductCategory[]
 * @var $list array
 */
?>
<?php slot('title', 'Каталог товаров') ?>

<?php slot('navigation') ?>
<?php include_component('productCatalog_', 'navigation') ?>
<?php end_slot() ?>

<?php //@todo rewrite to core api ?>

<ul>
  <?php foreach ($list as $i => $item): ?>
  <li style="margin-left: <?php echo ($item['level'] * 40) ?>px">
    <?php if (0 == $item['level']): ?>
    <strong><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></strong>
    <?php else: ?>
    <a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a>
    <?php endif ?>

    <?php if (isset($list[$i + 1]) && ($list[$i + 1]['level'] < $item['level']) && ($item['level'] < 3)): ?><br/>
    <br/><?php endif ?>
  </li>
  <?php endforeach ?>
</ul>

<?php slot('seo_counters_advance') ?>
<?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>
