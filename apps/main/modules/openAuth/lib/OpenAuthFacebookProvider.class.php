<?php

class OpenAuthFacebookProvider extends BaseOpenAuthProvider
{

  static public $name = 'facebook';

  public function getData()
  {
    return array(
      'app-id' => $this->getConfig('app_id'),
    );
  }

  public function getProfile(sfWebRequest $request)
  {
    $userProfile = false;
    $session = array();

    $validKeys = array('access_token', 'base_domain', 'expires', 'secret', 'session_key');
    $cookie = $request->getCookie($this->getCookieName());
    if ($cookie)
    {
      $session = array();
      parse_str(trim($cookie, '\\"'), $session);

      ksort($session);

      $payload = '';
      foreach ($session as $k => $v)
      {
        if ('sig' != $k)
        {
          $payload .= $k . '=' . $v;
        }
      }
      if (md5($payload . $this->getConfig('secret_key')) == $session['sig'])
      {
        $sourceId = intval($session['uid']);
        $accessToken = $session['access_token'];

        $userProfile = UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $sourceId);
        if (!$userProfile)
        {
          $userProfile = new UserProfile();
          $userProfile->fromArray(array(
            'type'      => self::$name,
            'source_id' => $sourceId,
          ));

          $userProfile->content = sfYaml::dump($this->getUserContent($accessToken));
        }
      }
    }

    return $userProfile;
  }

  protected function getUserContent($accessToken)
  {
		$url = $this->getConfig('api_url')
      .'/me?access_token='.$accessToken
    ;

		return json_decode(file_get_contents($url), true);
  }

  public function getCookieName()
  {
    return 'fbs_'.$this->getConfig('app_id');;
  }
}