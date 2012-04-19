<?php
/**
 * @var string $view
 * @var $sf_data
 * @var $ajax_flag
 * @var ProductCorePager|sfOutputEscaper $productPager
 */
$ajax_flag = isset($ajax_flag) ? $ajax_flag : false;
$list = $productPager->getResults();
?>

<?php if ($productPager->getNbResults() == 0) { ?>
<div class="clear"></div>
<p>нет товаров</p>
<?php
} else {
  if ($ajax_flag) {
      switch($view){
        case 'line':
        case 'compact':
          require '_list_ajax_compact_.php';
          break;
        case 'expanded':
          require '_list_ajax_expanded_.php';
          break;
      }
  } else {
    switch($view){
      case 'compact':
        require '_list_compact_.php';
        break;
      case 'expanded':
        require '_list_expanded_.php';
        break;
      case 'line':
        require '_list_line_.php';
        break;
      case 'view':
        require '_list_view_.php';
        break;
    }
  }
}