<?php

require_once(dirname(__FILE__).'/vendor/twitteroauth/twitteroauth.php');

class OpenAuthTwitterProvider extends BaseOpenAuthProvider
{
  static public $name = 'twitter';

  public function getSigninUrl()
  {
    $oauth = new TwitterOAuth($this->getConfig('consumer_key'), $this->getConfig('consumer_secret'));

    $token = $this->getRequestToken();

    $requestToken = $token['oauth_token'];
    $requestTokenSecret = $token['oauth_token_secret'];

    return $oauth->getAuthorizeURL($token);
  }

  public function getRequestToken()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $oauth = new TwitterOAuth($this->getConfig('consumer_key'), $this->getConfig('consumer_secret'));

    // получаем временные ключи для получения PIN'а
    $token = $oauth->getRequestToken(url_for('user_oauth_callback', array('provider' => self::$name), true));
    //$token = $oauth->getRequestToken();

    return $token;
  }

  public function getAccessToken($requestToken, $requestTokenSecret)
  {
    $oauth = new TwitterOAuth(
      $this->getConfig('consumer_key'),
      $this->getConfig('consumer_secret'),
      $requestToken,
      $requestTokenSecret
    );

    return $oauth->getAccessToken();
  }

  public function getUserProfile(sfWebRequest $request, myUser $user)
  {
    $userProfile = false;

    if ($request->hasParameter('denied') || empty($request['oauth_token']))
    {
      return false;
    }

    if ((!$user->getAttribute('oauth_'.self::$name.'_access_token')) && (!$user->getAttribute('oauth_'.self::$name.'_access_token_secret')))
    {
      $token = $this->getAccessToken($request['oauth_token'], $request['oauth_verifier']);

      $user->setAttribute('oauth_'.self::$name.'_access_token', $token['oauth_token']);
      $user->setAttribute('oauth_'.self::$name.'_access_token_secret', $token['oauth_token_secret']);
    }

    $response = $this->getUserContent($user->getAttribute('oauth_'.self::$name.'_access_token'), $user->getAttribute('oauth_'.self::$name.'_access_token_secret'));

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

  public function getUserContent($accessToken, $accessTokenSecret)
  {
    $return = array();

    $oauth = new TwitterOAuth(
      $this->getConfig('consumer_key'),
      $this->getConfig('consumer_secret'),
      $accessToken,
      $accessTokenSecret
    );

    $response = $oauth->get('account/verify_credentials');

    foreach (array(
      'name',
      'following',
      'utc_offset',
      'description',
      'time_zone',
      'created_at',
      'friends_count',
      'profile_image_url',
      'screen_name',
      'id_str',
      'lang',
      'id',
      'followers_count',
    ) as $name)
    {
      if (!isset($response[$name])) continue;

      $return[$name] = $response[$name];
    }

    return $return;
  }
}