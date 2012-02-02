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
  public function preExecute()
  {
    parent::postExecute();

    $this->getRequest()->setParameter('_template', 'shop');
  }
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

    if (!empty($request['region']))
    {
      $this->region = RegionTable::getInstance()->getByToken($request['region']);
    }
    else
    {
      $region = $this->getUser()->getRegion();//RegionTable::getInstance()->getDefault();
      $this->region = $region['region'];
    }
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
