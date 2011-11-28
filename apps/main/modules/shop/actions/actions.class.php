<?php

/**
 * shop actions.
 *
 * @package    enter
 * @subpackage shop
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class shopActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    // temporary route fix
    $shop = !empty($request['region']) ? ShopTable::getInstance()->getByToken($request['region']) : false;
    if ($shop)
    {
      $this->redirect(array('sf_route' => 'shop_show', 'sf_subject' => $shop), 301);
    }

    $this->region = !empty($request['region']) ? RegionTable::getInstance()->getByToken($request['region']) : RegionTable::getInstance()->getDefault();
    $this->forward404Unless($this->region, 'Region not found');
  }
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->shop = $this->getRoute()->getObject();
  }
}
