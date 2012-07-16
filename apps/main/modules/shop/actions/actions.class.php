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
      $region = RepositoryManager::getRegion()->getById($request['region']);
    }
    else{
      $region = false;
    }


    if(!$region || !$region->getId())
    {
      $region = $this->getUser()->getRegion('region');
    }
    $this->region = array(
      'name' => $region->getName('name'),
      'type' => $region->getType(),
      'id' => $region->getId(),
      'latitude' => $region->getLatitude(),
      'longitude' => $region->getLongitude(),
    );
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
    $this->region = array(
      'name' => $this->getUser()->getRegion('name'),
      'type' => $this->getUser()->getRegion('type'),
      'id' => $this->getUser()->getRegion('id'),
      'latitude' => $this->getUser()->getRegion('latitude'),
      'longitude' => $this->getUser()->getRegion('longitude'),
    );
  }
}
