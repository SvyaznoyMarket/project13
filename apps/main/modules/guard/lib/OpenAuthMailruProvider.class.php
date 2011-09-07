<?php

class OpenAuthMailruProvider extends BaseOpenAuthProvider
{
  static public $name = 'mailru';

  public function getSigninUrl()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
    $request = sfContext::getInstance()->getRequest();

    return strtr('https://connect.mail.ru/oauth/authorize?client_id={app_id}&response_type=code&redirect_uri={redirect_url}&host={host}', array(
      '{app_id}'       => $this->getConfig('app_id'),
      '{redirect_url}' => urlencode(url_for('user_oauth_callback', array('provider' => self::$name), true)),
      '{host}'         => urlencode('http://'.$request->getHost()),
    ));
  }

  public function getUserProfile(sfWebRequest $request, myUser $user)
  {
    $userProfile = false;

    $code = $request['code'];
    if ($request->hasParameter('error') || empty($code))
    {
      return false;
    }

    $response = $this->getAccessTokenResponse($code);
    if (empty($response['access_token']))
    {
      return false;
    }

    $id = !empty($response['x_mailru_vid']) ? $response['x_mailru_vid'] : false;
    $userProfile = $id ? UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $id) : false;
    if (!$userProfile)
    {
        $userProfile = new UserProfile();
        $userProfile->fromArray(array(
          'type'      => self::$name,
          'source_id' => $id,
        ));

        $userProfile->content = sfYaml::dump($this->getUserContent($response['access_token'], $id));
    }

    return $userProfile;
  }

  public function getAccessTokenResponse($code)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $data = http_build_query(array(
      'client_id'     => $this->getConfig('app_id'),
      'client_secret' => $this->getConfig('secret_key'),
      'grant_type'    => 'authorization_code',
      'code'          => $code,
      'redirect_uri'  => url_for('user_oauth_callback', array('provider' => self::$name), true),
    ));

    $response = file_get_contents('https://connect.mail.ru/oauth/token', false, stream_context_create(array('http' => array(
      'method'  => 'POST',
      'header'  => "Content-type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen($data)."\r\nAccept: */*\r\n",
      'content' => $data,
    ))));

    return json_decode($response, true);
  }

  public function getUserContent($sessionKey, $id)
  {
   $content = array();

    $params = array(
      'method'      => 'users.getInfo',
      'app_id'      => $this->getConfig('app_id'),
      'session_key' => $sessionKey,
      'uids'        => $id,
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

  protected function getSig($requestParams)
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
}