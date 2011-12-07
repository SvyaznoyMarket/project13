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

  public function getCart()
  {
    return $this->getUserData('cart');
  }
  
  public function getCartBaseInfo()
  {
    $cart = $this->getCart();
    $result['qty'] = 0;
    $result['sum'] = 0;
    $result['productsInCart'] = array();
    $result['servicesInCart'] = array();
    $cart = $this->getCart();
    if (!$cart || !$cart->getProducts()) {
       # return $result;
    }
   # myDebug::dump($cart->getProducts());
    
    foreach($cart->getProducts()->toArray() as $id => $product){
      $result['qty'] += $product['cart']['quantity'];
      $result['sum'] += $product['price'] * $product['cart']['quantity'];
      $result['productsInCart'][ $product['token'] ] = $product['cart']['quantity'];          
    }        
    foreach($cart->getServices()->toArray() as $id => $service){
      $qty = $service['cart']['quantity'];
      if ($qty > 0) {
        $result['servicesInCart'][ $service['token'] ][0] = $qty;          
      }
      foreach($service['cart']['product'] as $pId => $pQty) {
          $token = false;
          foreach($cart->getProducts()->toArray() as $id => $product){
              #echo $product['id'] .'=='. $pId."\n";
              if ($product['id'] == $pId) {
                  $token = $product['token'];
                  break;
              }
          }
          if ($token && $pQty) {
            $result['servicesInCart'][ $service['token'] ][$token] = $pQty;          
          }
      }
    }        
    return $result;
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
      'core_id'   => $region->core_id,
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

public function getRealIpAddr()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) // Определение IP-адреса
  {
    $ip=$_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) // Проверка того, что IP идёт через прокси
  {
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
    $ip=$_SERVER['REMOTE_ADDR'];
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

  public function setCacheCookie()
  {
    $sessionId = session_id();
    $key = md5(strval($sessionId).strval(time()));
    sfContext::getInstance()->getResponse()->setCookie(sfConfig::get('app_guard_cache_cookie_name', 'enter_cache'), $key, null);
  }
}
