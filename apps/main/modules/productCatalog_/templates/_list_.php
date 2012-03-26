<?php
/**
 * @var $view
 * @var $sf_dataw
 * @var ProductCorePager|sfOutputEscaper $productPager
 */
$ajax_flag = isset($ajax_flag) ? $ajax_flag : false;
$productPager = $productPager->getRawValue();
$list = $productPager->getResults();
?>

<?php if (count($list) == 0) { ?>
<div class="clear"></div>
<p>нет товаров</p>
<?php
} else {
  if ($ajax_flag) {
    if ($view == 'line') {
      $include = 'list_ajax_compact_';
    } else {
      $include = 'list_ajax_' . $view . '_';
    }
    include_partial($include, array('list' => $list));
  } else {
    include_partial('list_' . $view . '_', array('list' => $list));
  }
}