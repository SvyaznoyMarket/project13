<?php

class myUser extends myGuardSecurityUser
{

  const DEFAULT_REGION_ID = 14974;

  protected
    $cart = null,
    $order = null,
    $region = null;

  public function shutdown()
  {
    foreach (array('order') as $name)
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
    if (null == $this->cart) {
      $this->cart = new UserCartNew();
    }

    return $this->cart;
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
      $regionCoreId = (int)sfContext::getInstance()->getRequest()->getCookie(sfConfig::get('app_guard_region_cookie_name', 'geoshop'));

      if(!$this->setRegion($regionCoreId)){
        $this->setRegion(self::DEFAULT_REGION_ID);
      }
    }

    if (!$this->region) {
      return null;
    }
    #@TODO: если нет запрашиваемого ключа то лучше выкидывать ошибку
    return !empty($key) ? $this->region[$key] : $this->region;
  }

  public function getRegion_()
  {
    return $this->getRegion();
  }

  public function getRegionCoreId() //Этот метод дает Автосохранение в куках для дальнейшего использования вне симфони
  {
    $region = $this->getRegion();

    return $region['core_id'];
  }

  public function setRegion($region_id)
  {
    $region = RepositoryManager::getRegion()->getById((int)$region_id);

    if(!$region){
      return false;
    }

    $parentRegion = RepositoryManager::getRegion()->getById((int)$region->getParentId());

    $r = CoreClient::getInstance()->query('geo/get', array(
      'id' => array($region->getId()),
    ));

    $parentName = ((bool)$parentRegion)?$parentRegion->getName() : '';

    /** @var region RegionEntity */

    $this->region = array(
      'id' => $region->getId(),
      'name' => $region->getName(),
      'full_name' => $region->getName() . ', ' . $parentName,
      'type' => $region->getType(),
      'product_price_list_id' => $region->getPriceListId(),
      'core_id' => $region->getId(),
      'latitude' => $region->getLatitude(),
      'longitude' => $region->getLongitude(),
      'region' => $region,
      'has_f1' => isset($r[0]) ? ((bool)$r[0]['has_f1']) : false,
    );

    #@TODO: зачем кука устанавливается два раза ?
    $this->setRegionCookie();
    return true;
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

    if($name == 'cart'){
      return $this->getCart();
    }
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
