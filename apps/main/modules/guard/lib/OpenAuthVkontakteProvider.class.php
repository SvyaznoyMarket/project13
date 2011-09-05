<?php

class OpenAuthVkontakteProvider extends BaseOpenAuthProvider
{
  static public $name = 'vkontakte';

  public function getSigninUrl()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    return strtr('{api_url}/oauth/authorize?client_id={app_id}&scope={permissions}&redirect_uri={redirect_url}&display=page&response_type=code', array(
      '{api_url}'      => $this->getConfig('api_url'),
      '{app_id}'       => $this->getConfig('app_id'),
      '{permissions}'  => $this->getConfig('permissions'),
      '{redirect_url}' => urlencode(url_for('user_oauth_callback', array('provider' => self::$name), true)),
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
    if (isset($response['error']) || empty($response['access_token']))
    {
      return false;
    }

    $id = !empty($response['user_id']) ? $response['user_id'] : false;
    $userProfile = $id ? UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $id) : false;
    if (!$userProfile)
    {
        $userProfile = new UserProfile();
        $userProfile->fromArray(array(
          'type'      => self::$name,
          'source_id' => $id,
        ));

        $userProfile->content = sfYaml::dump($this->getUserContent($id));
    }

    return $userProfile;
  }

  public function getAccessTokenResponse($code)
  {
    $url = strtr('{api_url}/oauth/access_token?client_id={app_id}&client_secret={secret_key}&code={code}', array(
      '{api_url}'    => $this->getConfig('api_url'),
      '{app_id}'     => $this->getConfig('app_id'),
      '{secret_key}' => $this->getConfig('secret_key'),
      '{code}'       => $code,
    ));

    return json_decode(file_get_contents($url), true);
  }

  public function getUserContent($id)
  {
    $url = $this->getConfig('api_url')
      .'/method/getProfiles?'.http_build_query(array(
        'uids'   => $id,
        'fields' => 'uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,has_mobile,contacts,education',
      ));
    ;

    $response = json_decode(file_get_contents($url), true);

    return isset($response['response'][0]) ? $response['response'][0] : false;
  }
}