<?php

class myUser extends myGuardSecurityUser
{

  protected
    $cart = null,
    $order = null,
    $region = null;

  public function shutdown()
  {
    foreach (array('cart', 'order') as $name)
    {
      $object = call_user_func(array($this, 'get' . ucfirst($name)));
      $this->setAttribute($name, $object->dump());
    }

    parent::shutdown();
  }

  public function signIn($user, $remember = false, $con = null)
  {
    parent::signIn($user, $remember, $con);

    $this->setCacheCookie();
  }

  public function signOut()
  {
    $this->setCacheCookie();

    parent::signOut();
  }

  /**
   * @return UserCart
   */
  public function getCart()
  {
    return $this->getUserData('cart');
  }

  /**
   * @return UserOrder
   */
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
    if (!$this->region) {
      $regionCoreId = sfContext::getInstance()->getRequest()->getCookie(sfConfig::get('app_guard_region_cookie_name', 'geoshop'));

      $region = !empty($regionCoreId)?RegionTable::getInstance()->findOneBy('core_id', $regionCoreId):Null;
      if (!$region || !$region->isCity()) {
        $region = RegionTable::getInstance()->getDefault();
        $this->setRegion($region->id);
      }

      $this->region = array(
        'id' => $region->id,
        'name' => $region->name,
        'full_name' => $region->name . ', ' . $region->getParent()->name,
        'type' => $region->type,
        'product_price_list_id' => $region->product_price_list_id,
        'core_id' => $region->core_id,
        'latitude' => $region->latitude,
        'longitude' => $region->longitude,
        'region' => $region,
      );
    }

    #@TODO: если нет запрашиваемого ключа то лучше выкидывать ошибку
    return !empty($key) ? $this->region[$key] : $this->region;
  }

  public function getRegion_()
  {
    if (!$this->region) {
      $region = false;
      $region_id = $this->getAttribute('region', null);

      if ($region_id) {
        $region = RepositoryManager::getRegion()->getById($region_id);
      }

      if (!$region) {
        $regionData = sfContext::getInstance()->getRequest()->getParameter('geoip');
        $region =
          (!empty($regionData['region']) && !empty($regionData['name']))
            ? RepositoryManager::getRegion()->getByToken($regionData['region'] . '-' . $regionData['name'])
            : RepositoryManager::getRegion()->getOneDefault();

        $this->setRegion($region->getId());
      }

      $this->region = $region;
    }

    return $this->region;
  }

  public function getRegionCoreId() //Этот метод дает Автосохранение в куках для дальнейшего использования вне симфони
  {
    $region = $this->getRegion();

    return $region['core_id'];
  }

  public function setRegion($region_id)
  {
    $region = RegionTable::getInstance()->findOneBy('id', $region_id);
    if(!$region){
      return false;
    }
    $this->region = array(
      'id' => $region->id,
      'name' => $region->name,
      'full_name' => $region->name . ', ' . $region->getParent()->name,
      'type' => $region->type,
      'product_price_list_id' => $region->product_price_list_id,
      'core_id' => $region->core_id,
      'region' => $region,
    );
    #@TODO: зачем кука устанавливается два раза ?
    $this->setRegionCookie();
  }

  public function getIp()
  {
    $ip = $this->getAttribute('ip', null);

    if (!$ip) {
      $geoip = sfContext::getInstance()->getRequest()->getParameter('geoip');
      $ip = $geoip['ip_address'];
      $this->setAttribute('ip', $ip);
    }

    return $ip;
  }

  public function getRealIpAddr()
  {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) // Определение IP-адреса
    {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) // Проверка того, что IP идёт через прокси
    {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  protected function getUserData($name)
  {
    if (null == $this->$name) {
      $class = sfInflector::camelize('user_' . $name);
      $this->$name = new $class($this->getAttribute($name, array()));
    }

    return $this->$name;
  }

  public function setCacheCookie()
  {
    $sessionId = session_id();
    $key = md5(strval($sessionId) . strval(time()));
    sfContext::getInstance()->getResponse()->setCookie(sfConfig::get('app_guard_cache_cookie_name', 'enter_cache'), $key, null);
  }

  public function setRegionCookie()
  {
    $key = $this->getRegion('core_id');
    sfContext::getInstance()->getResponse()->setCookie(sfConfig::get('app_guard_region_cookie_name', 'geoshop'), $key, time() + 60 * 60 * 24 * 365);
  }

}
