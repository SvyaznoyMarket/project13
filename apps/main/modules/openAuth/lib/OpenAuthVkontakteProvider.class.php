<?php

class OpenAuthVkontakteProvider extends BaseOpenAuthProvider
{

  static public $name = 'vkontakte';

  public function getData()
  {
    return array(
      'app-id' => $this->getConfig('app_id'),
    );
  }

  /**
   * http://vkontakte.ru/developers.php?oid=-1&p=VK.Auth
   *
   */
  public function getProfile(sfWebRequest $request)
  {
    $userProfile = false;
    $session = array();

    $validKeys = array('expire', 'mid', 'secret', 'sid', 'sig');
    $cookie = $request->getCookie($this->getCookieName());
    if ($cookie)
    {
      $sessionData = explode('&', $cookie, 10);
      foreach ($sessionData as $pair)
      {
        list($key, $value) = explode('=', $pair, 2);
        if (empty($key) || empty($value) || !in_array($key, $validKeys))
        {
          continue;
        }

        $session[$key] = $value;
      }
      foreach ($validKeys as $key)
      {
        if (!isset($session[$key]))
        {
          return $userProfile;
        }
      }
      ksort($session);

      $sign = '';
      foreach ($session as $key => $value)
      {
        if ('sig' != $key)
        {
          $sign .= ($key.'='.$value);
        }
      }
      $sign .= $this->getConfig('secret_key');
      $sign = md5($sign);
      if ($session['sig'] == $sign && $session['expire'] > time())
      {
        $sourceId = intval($session['mid']);

        $userProfile = UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $sourceId);
        if (!$userProfile)
        {
          $userProfile = new UserProfile();
          $userProfile->fromArray(array(
            'type'      => self::$name,
            'source_id' => $sourceId,
          ));

          $userProfile->content = sfYaml::dump($this->getUserContent($sourceId));
        }
      }
    }

    return $userProfile;
  }

  public function getUserContent($sourceId)
  {
    $url = $this->getConfig('api_url')
      .'/method/getProfiles?'.http_build_query(array(
        'uids'   => $sourceId,
        'fields' => 'uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,has_mobile,contacts,education',
      ));
    ;
    $response = json_decode(file_get_contents($url), true);

    return isset($response['response'][0]) ? $response['response'][0] : array();
  }

  public function query($method, array $params = array())
  {
    $params = myToolkit::arrayDeepMerge($params, array(
      'v'         => '3.0',
      'format'    => 'json',
    ));


  }

  public function getCookieName()
  {
    return 'vk_app_'.$this->getConfig('app_id');
  }
}