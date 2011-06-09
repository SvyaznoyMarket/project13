<?php

/**
 * productCard actions.
 *
 * @package    enter
 * @subpackage productCard
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCardActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();

    // история просмотра товаров
    $this->getUser()->getProductHistory()->addProduct($this->product);
  }
}
