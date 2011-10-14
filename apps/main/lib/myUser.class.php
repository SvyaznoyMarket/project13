<?php

class myUser extends myGuardSecurityUser
{
  protected
    $cart = null,
    $productHistory = null,
    $productCompare = null,
    $order = null
  ;

  public function shutdown()
  {
    foreach (array('cart', 'productHistory', 'productCompare', 'order') as $name)
    {
      $object = call_user_func(array($this, 'get'.ucfirst($name)));
      $this->setAttribute($name, $object->dump());
    }

    parent::shutdown();
  }

  public function getCart()
  {
    return $this->getUserData('cart');
  }

  public function getProductHistory()
  {
    return $this->getUserData('productHistory');
  }

  public function getProductCompare()
  {
    return $this->getUserData('productCompare');
  }

  public function getOrder()
  {
    return $this->getUserData('order');
  }

  public function getType()
  {
    return $this->isAuthenticated() ? $this->getGuardUser()->type : null;
  }

  public function setProfile(UserProfile $userProfile)
  {
    $this->setAttribute('profile', $userProfile);
  }

  public function getProfile()
  {
    return $this->getAttribute('profile', false);
  }

  public function getRegion($key = null)
  {
    $region_id = $this->getAttribute('region', null);
    if ($region_id)
    {
      $region = RegionTable::getInstance()->findOneByIdAndType($region_id, 'city');
    }

    if (!isset($region) || !$region)
    {
      $geoip = sfContext::getInstance()->getRequest()->getParameter('geoip');
      $region = !empty($geoip['city_name']) ? RegionTable::getInstance()->findOneByName($geoip['city_name']) : null;
      if (!$region)
      {
        $region = RegionTable::getInstance()->getDefault();
        $this->setAttribute('region', $region->id);
      }
    }
    $parent_region = $region->getNode()->getParent();

    $result = array(
      'id'        => $region->id,
      'name'      => $region->name,
      'full_name' => $region->name.', '.$parent_region->name,
      'type'      => $region->type,
    );

    return !empty($key) ? $result[$key] : $result;
  }

  public function getIp()
  {
    $ip = $this->getAttribute('ip', null);

    if (!$ip)
    {
      $geoip = sfContext::getInstance()->getRequest()->getParameter('geoip');
      $ip = $geoip['ip_address'];
      $this->setAttribute('ip', $ip);
    }

    return $ip;
  }

  protected function getUserData($name)
  {
    if (null == $this->$name)
    {
      $class = sfInflector::camelize('user_'.$name);
      $this->$name = new $class($this->getAttribute($name, array()));
    }

    return $this->$name;
  }
}
