<?php
/**
 * @var $view
 * @var $item
 * @var $ii
 * @var $maxPerPage
 * @var $show_model
 */
switch($view){
  case 'compact':
    require '_show_compact_.php';
    break;
  case 'extra_compact':
      require '_show_extra_compact.php';
      break;
  case 'expanded':
    require '_show_expanded_.php';
    break;
  case 'line':
    require '_show_line_.php';
    break;
  case 'default':
      require '_show_default_.php';
      break;
}
