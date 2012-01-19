<?php

class OpenAuthOdnoklassnikiProvider extends BaseOpenAuthProvider
{

  static public $name = 'odnoklassniki';

  public function getSigninUrl()
  {
    return strtr('http://www.odnoklassniki.ru/oauth/authorize?client_id={app_id}&scope={permissions}&response_type=code&redirect_uri={redirect_url}', array(
      '{api_url}'      => $this->getConfig('api_url'),
      '{app_id}'       => $this->getConfig('app_id'),
      '{permissions}'  => $this->getConfig('permissions'),
      '{redirect_url}' => urlencode($this->generateUrl('user_oauth_callback', array('provider' => self::$name), true)),
    ));
  }

  public function getUserProfile(sfWebRequest $request, myUser $user)
  {
    $userProfile = null;

    $code = $request['code'];
    if (empty($code))
    {
      return $userProfile;
    }

    $response = $this->getAccessTokenResponse($code);
    if (empty($response['error']) && !empty($response['access_token']))
    {
      $params = array(
        'client_id='.$this->getConfig('app_id'),
        'application_key='.$this->getConfig('public_key'),
      );
      sort($params);
      $sig = md5(join('', $params) . md5($response['access_token'] . $this->getConfig('private_key')));

      $url = $this->getConfig('api_url')
        .'/api/users/getCurrentUser?'
        .'access_token='.$response['access_token']
        .'&'.join('&', $params)
        .'&sig='.$sig
        .'&'.join('&', $params)
      ;
      $response = json_decode(file_get_contents($url), true);

      $id = !empty($response['uid']) ? $response['uid'] : false;
      $userProfile = $id ? UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $id) : false;
      if (!$userProfile)
      {
        $userProfile = new UserProfile();
        $userProfile->fromArray(array(
          'type'      => self::$name,
          'source_id' => $id,
        ));

        $userProfile->content = sfYaml::dump($this->getUserContent($response));
      }

    }

    return $userProfile;
  }

  public function getAccessTokenResponse($code)
  {
    $data = http_build_query(array(
      'code'          => $code,
      'redirect_uri'  => $this->generateUrl('user_oauth_callback', array('provider' => self::$name), true),
      'grant_type'    => 'authorization_code',
      'client_id'     => $this->getConfig('app_id'),
      'client_secret' => $this->getConfig('private_key'),
    ));

    $response = file_get_contents($this->getConfig('access_token_url').'?', false, stream_context_create(array('http' => array(
      'method'  => 'POST',
      'header'  => "Content-type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen($data)."\r\n",
      'content' => $data,
    ))));

    return json_decode($response, true);
  }



  protected function getUserContent($response)
  {
    $content = array();

    foreach (array(
      'uid',
      'birthday',
      'age',
      'first_name',
      'last_name',
      'name',
      'gender',
      'has_email',
      'pic_1',
      'pic_2',
    ) as $name)
    {
      if (!isset($response[$name])) continue;

      $content[$name] = $response[$name];
    }

    return $content;
  }
}