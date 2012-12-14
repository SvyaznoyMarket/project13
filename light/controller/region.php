<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 17.05.12
 * Time: 12:47
 * To change this template use File | Settings | File Templates.
 */
require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');

class regionController
{
  public function getShopAvailable(Response $response, $params = array()){
    TimeDebug::start('controller:region:getShopAvailable');
    $regionList = App::getRegion()->getShowInMenu();

    $currentRegionId = App::getCurrentUser()->getRegion()->getId();
    $return = array();

    foreach ($regionList as $region)
    {
      $entity = array(
        'name' => $region->getName(),
        'link' => '/region/change/' . $region->getToken(),
      );
      if ($region->getId() == $currentRegionId) {
        $entity['is_active'] = 'active';

        if(!App::getCurrentUser()->isSelectedRegion()){
          App::getCurrentUser()->setRegionById($region->getId());
        }
      }
      $return[] = $entity;
    }

    $response->setContentType('application/json');
    $response->setContent(json_encode(array('success' => true, 'data' => $return)));
    TimeDebug::end('controller:region:getShopAvailable');
  }
}