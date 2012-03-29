<?php
/**
 * @var $productPager
 * @var $view
 * @var $allOk
 */
if ($allOk)
  include_partial('list_', array('productPager' => $productPager, 'ajax_flag' => true, 'view' => $view,));
