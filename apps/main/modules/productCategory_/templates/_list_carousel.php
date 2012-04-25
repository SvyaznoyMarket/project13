<?php
/**
 * @var $categoryTagList
 * @var $maxPerPage
 */
?>

<?php if(count($categoryTagList) > 0):?>
  <?php foreach ($categoryTagList as $productTagCategory)
    render_partial('productCategory_/templates/_show_carousel.php', array(
      'productTagCategory' => $productTagCategory,
      'maxPerPage' => $maxPerPage,
    ));
  ?>
<?php else: ?>
  <p>Нет категорий</p>
<?php endif ?>
