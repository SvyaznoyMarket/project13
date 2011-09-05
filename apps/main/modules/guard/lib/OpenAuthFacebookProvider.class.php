<?php

class OpenAuthFacebookProvider extends BaseOpenAuthProvider
{
  static public $name = 'facebook';

  public function getSigninUrl()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    return strtr('https://www.facebook.com/dialog/oauth?client_id={app_id}&redirect_uri={redirect_url}&scope={permissions}', array(
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

    $response = $this->getUserContent($response['access_token']);
    $id = !empty($response['id']) ? $response['id'] : false;
    $userProfile = $id ? UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $id) : false;
    if (!$userProfile)
    {
        $userProfile = new UserProfile();
        $userProfile->fromArray(array(
          'type'      => self::$name,
          'source_id' => $id,
        ));

        $userProfile->content = sfYaml::dump($response);
    }

    return $userProfile;
  }

  public function getAccessTokenResponse($code)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $url = strtr('{api_url}/oauth/access_token?client_id={app_id}&redirect_uri={redirect_url}&client_secret={secret_key}&code={code}', array(
      '{api_url}'      => $this->getConfig('api_url'),
      '{app_id}'       => $this->getConfig('app_id'),
      '{redirect_url}' => urlencode(url_for('user_oauth_callback', array('provider' => self::$name), true)),
      '{secret_key}'   => $this->getConfig('secret_key'),
      '{code}'         => $code,
    ));

    $response = file_get_contents($url);
    parse_str($response, $return);

    return $return;
  }

  public function getUserContent($token)
  {
    $url = $this->getConfig('api_url')
      .'/me?access_token='.$token
    ;

    $response = json_decode(file_get_contents($url), true);

    return $response;
  }
}