<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.06.12
 * Time: 17:19
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(VIEW_PATH.'dataObject/ServiceData.php');

class ServiceModel
{

  /**
   * @param int[] $idList
   * @param $callback
   * @return ServiceData[]
   */
  public function getServicesByIdListAsync($idList, $callback){
    $ids = array();
    foreach($idList as $id){
      $ids[] = (int) $id;
    }
    $ids = array_unique($ids);

    if(count($ids) < 1){
      $callback(array());
      return;
    }

    $cb = function($response) use (&$callback)
    {
      $list = array();
      foreach ($response as $item)
        $list[] = new ServiceData($item);
      $callback($list);
    };

    App::getCoreV2()->addQuery('service/get', array(
      'id' => $ids,
      'geo_id' => App::getCurrentUser()->getRegion()->getId(),
    ), array(), $cb);
  }

}
