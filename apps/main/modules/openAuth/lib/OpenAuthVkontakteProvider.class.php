<?php

class OpenAuthVkontakteProvider extends BaseOpenAuthProvider
{

  static public $name = 'vkontakte';

  public function getData()
  {
    $config = $this->getConfig();

    return array(
      'app-id' => $config['app_id'],
    );
  }

  /**
   * http://vkontakte.ru/developers.php?oid=-1&p=VK.Auth
   *
   */
  public function getProfile(sfWebRequest $request)
  {
    $config = $this->getConfig();

    $userProfile = false;
    $session = array();

    $validKeys = array('expire', 'mid', 'secret', 'sid', 'sig');
    $cookie = $request->getCookie('vk_app_'.$config['app_id']);
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
      $sign .= $config['secret_key'];
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

          $userProfile->content = sfYaml::dump($this->getContent($sourceId));
        }
      }
    }

    return $userProfile;
  }

  protected function getContent($sourceId)
  {
    $config = $this->getConfig();

    /*
    $url = $config['api_url']
      .'/method/getProfiles'
      .'?uids='.$sourceId
      .'&fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,has_mobile,contacts,education'
    ;
    */

    $response = $this->query('getProfiles', array(
      'uids'   => $sourceId,
      'fields' => 'uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,has_mobile,contacts,education',
    ));

    return isset($response['response'][0]) ? $response['response'][0] : array();
  }

  public function query($method, array $params = array())
  {
    $config = $this->getConfig();

    function getQueryString($params)
    {
      $pice = array();
      foreach($params as $k => $v)
      {
        $pice[] = $k.'='.urlencode($v);
      }
      return implode('&', $pice);
    }

    $params = myToolkit::arrayDeepMerge($params, array(
      //'api_id'    => $config['app_id'],
      'v'         => '3.0',
      //'method'    => $method,
      //'timestamp' => time(),
      'format'    => 'json',
      //'random'    => rand(0, 10000),
    ));

    /*
		ksort($params);
		$sig = '';
		foreach($params as $k => $v)
    {
			$sig .= $k.'='.$v;
		}
		$sig .= $config['secret_key'];
		$params['sig'] = md5($sig);
    */
		$query = $config['api_url'].'/method/'.$method.'?'.getQueryString($params);

		return json_decode(file_get_contents($query), true);
  }

}