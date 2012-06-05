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
    $params = array('slug' => $token);
    $result = CoreClient::getInstance()->query('service.get', $params);

    if (!$result) {
      return null;
    }

    $service = new ServiceEntity($result);

    return $service;
  }

}
