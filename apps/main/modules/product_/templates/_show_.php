<?php
/**
 * @var $view
 * @var $sf_data
 * @var $item
 * @var $ii
 * @var $maxPerPage
 */
switch($view){
  case 'compact':
    require '_show_compact_.php';
    break;
  case 'expanded':
    require '_show_expanded_.php';
    break;
  case 'line':
    require '_show_line_.php';
}
