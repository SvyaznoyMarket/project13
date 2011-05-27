<?php

/**
 * default actions.
 *
 * @package    enter
 * @subpackage default
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->productList = ProductTable::getInstance()->getList(array(
      'order' => 'product.name',
      //'order' => 'product.id',
      'limit' => 100,
      'view'  => 'list'
    ));
  }
}
