<?php

class OpenAuthMailruProvider extends BaseOpenAuthProvider
{

  static public $name = 'mailru';

  public function getData()
  {
    //sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
    //$request = sfContext::getInstance()->getRequest();

    return array(
      'app-id'      => $this->getConfig('app_id'),
      'private-key' => $this->getConfig('private_key'),
      /*
      'url' => strtr('https://connect.mail.ru/oauth/authorize?client_id={app_id}&response_type=token&redirect_uri={redirect_uri}&host={host}', array(
        '{app_id}'       => $this->getConfig('app_id'),
        '{redirect_uri}' => urlencode(url_for($this->getConfig('redirect_uri'), true)),
        '{host}'         => 'http://'.$request->getHost()
      )),
      */
    );
  }

  public function getProfile(sfWebRequest $request)
  {
    $userProfile = false;

    parse_str(urldecode($request->getCookie($this->getCookieName())), $requestParams);

    if ($this->getSig($requestParams) ==  $requestParams['sig'])
    {
      $sourceId = $requestParams['vid'];

      $userProfile = UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $sourceId);
      if (!$userProfile)
      {
        $userProfile = new UserProfile();
        $userProfile->fromArray(array(
          'type'      => self::$name,
          'source_id' => $sourceId,
        ));

        $userProfile->content = sfYaml::dump($this->getUserContent($requestParams['session_key'], $sourceId));
      }
    }

    return $userProfile;
  }

  protected function getUserContent($sessionKey, $sourceId)
  {
    $content = array();

    $params = array(
      'method'      => 'users.getInfo',
      'app_id'      => $this->getConfig('app_id'),
      'session_key' => $sessionKey,
      'uids'        => $sourceId,
      'secure'      => '1',
    );

    $url = $this->getConfig('api_url').'?'
      .http_build_query($params)
      .'&sig='.$this->getSig($params)
    ;
    $response = json_decode(file_get_contents($url), true);
    if (isset($response[0]))
    {
      foreach (array(
        'nick',
        'pic_big',
        'last_name',
        'has_pic',
        'email',
        'vip',
        'birthday',
        'link',
        'uid',
        'location',
        'sex',
        'pic',
        'pic_small',
        'first_name',
      ) as $name)
      {
        if (!isset($response[0][$name])) continue;

        $content[$name] = $response[0][$name];
      }
    }

    return $content;
  }

  public function getSig(array $requestParams)
  {
    ksort($requestParams);
    $params = '';
    foreach ($requestParams as $k => $v)
    {
      if ('sig' != $k)
      {
        $params .= "$k=$v";
      }
    }

    return md5($params . $this->getConfig('secret_key'));
  }

  public function getCookieName()
  {
    return 'mrc';
  }
}