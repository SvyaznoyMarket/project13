<?php

require_once(dirname(__FILE__).'/vendor/twitteroauth/twitteroauth.php');

class OpenAuthTwitterProvider extends BaseOpenAuthProvider
{

  static public $name = 'twitter';

  public function getData()
  {
    return array();
  }

  public function getUserProfile($accessToken, $accessTokenSecret)
  {
    $userProfile = false;

    $oauth = new TwitterOAuth(
      $this->getConfig('consumer_key'),
      $this->getConfig('consumer_secret'),
      $accessToken,
      $accessTokenSecret
    );

    $result = $oauth->get('account/verify_credentials');

    $sourceId = !empty($result['id']) ? $result['id'] : false;
    if ($sourceId)
    {
      $userProfile = UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $sourceId);
      if (!$userProfile)
      {
        $userProfile = new UserProfile();
        $userProfile->fromArray(array(
          'type'      => self::$name,
          'source_id' => $sourceId,
        ));

        $userProfile->content = sfYaml::dump($this->getUserContent($result));
      }
    }

    return $userProfile;
  }

  public function getUserContent($data)
  {
    $content = array();

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
      if (!isset($data[$name])) continue;

      $content[$name] = $data[$name];
    }

    return $content;
  }

  public function getRequestToken()
  {
    //sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $oauth = new TwitterOAuth($this->getConfig('consumer_key'), $this->getConfig('consumer_secret'));

    // получаем временные ключи для получения PIN'а
    //$token = $oauth->getRequestToken(url_for($this->getConfig('callback_url'), true));
    return $oauth->getRequestToken();
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

  public function getSigninUrl($token)
  {
    $oauth = new TwitterOAuth($this->getConfig('consumer_key'), $this->getConfig('consumer_secret'));

    $requestToken = $token['oauth_token'];
    $requestTokenSecret = $token['oauth_token_secret'];

    return $oauth->getAuthorizeURL($token);
  }
}