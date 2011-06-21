<?php

/**
 * userProductHistory components.
 *
 * @package    enter
 * @subpackage userProductHistory
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductHistoryComponents extends myComponents
{
 /**
  * Executes show component
  *
  */
  public function executeShow()
  {
    $this->setVar('productList', $this->getUser()->getProductHistory()->getProducts(), true);
  }
}
