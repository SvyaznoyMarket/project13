<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 05.06.12
 * Time: 12:29
 * To change this template use File | Settings | File Templates.
 */
class ServiceRepository
{

  /**
   * @param string $token
   * @return null|ServiceEntity
   */
  public function getByToken($token)
  {
    $params = array('slug' => $token, 'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId());
    $result = CoreClient::getInstance()->query('service.get', $params);

    if (!$result || !array_key_exists($token, $result)) {
      return null;
    }

    $service = new ServiceEntity($result[$token]);

    return $service;
  }

  /**
   * @param integer $id
   * @return null|ServiceEntity
   */
  public function getById($id)
  {
    $params = array('id' => $id, 'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId());
    $result = CoreClient::getInstance()->query('service.get', $params);

    if (!$result || !array_key_exists($id, $result)) {
      return null;
    }

    $service = new ServiceEntity($result[$id]);

    return $service;
  }

  /**
   * Load ServiceEntity by id from core.
   *
   * @param $callback
   * @param array $idList
   * @return ServiceEntity[]
   */
  public function getListByIdAsync($callback, array $idList)
  {
    $idList = array_unique($idList);
    if (empty($idList)){
      $callback(array());
      return;
    }

    $cb = function($response) use (&$callback)
    {
      $list = array();
      foreach ($response as $item)
        $list[] = new ServiceEntity($item);
      $callback($list);
    };

    CoreClient::getInstance()->addQuery('service/get', array(
      'id' => $idList,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ), array(), $cb);
  }


}
