<?php

/**
 * productStock actions.
 *
 * @package    enter
 * @subpackage productStock
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productStockActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();

    $title = '«Где купить»: ' . $this->product['name'] . ' в магазинах "Enter"';
    $mainCategory = $this->product->getMainCategory();
    $title .= ' – '.$mainCategory;
    if ($mainCategory)
    {
      $rootCategory = $mainCategory->getRootCategory();
      if ($rootCategory->id !== $mainCategory->id)
      {
        $title .= ' – '.$rootCategory;
      }
    }
    $this->getResponse()->setTitle($title.' – Enter.ru');
  }
}
