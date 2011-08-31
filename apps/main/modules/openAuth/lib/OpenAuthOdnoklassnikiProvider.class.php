<?php

class OpenAuthOdnoklassnikiProvider extends BaseOpenAuthProvider
{

  static public $name = 'odnoklassniki';

  public function getData()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    return array(
      'app-id'     => $this->getConfig('app_id'),
      'return-url' => url_for($this->getConfig('return_url'), true),
    );
  }

  public function getProfile(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $userProfile = null;

    if (empty($request['code']))
    {
      return $userProfile;
    }

    $data = http_build_query(array(
      'code'          => $request['code'],
      'redirect_uri'  => url_for($this->getConfig('return_url'), true),
      'grant_type'    => 'authorization_code',
      'client_id'     => $this->getConfig('app_id'),
      'client_secret' => $this->getConfig('private_key'),
    ));

    $response = file_get_contents($this->getConfig('access_token_url').'?', false, stream_context_create(array('http' => array(
      'method'  => 'POST',
      'header'  => "Content-type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen($data)."\r\n",
      'content' => $data,
    ))));
    $response = json_decode($response, true);
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
      if (!empty($response['uid']))
      {
        $sourceId = $response['uid'];
        $userProfile = UserProfileTable::getInstance()->findOneByTypeAndSourceId(self::$name, $sourceId);
        if (!$userProfile)
        {
          $userProfile = new UserProfile();
          $userProfile->fromArray(array(
            'type'      => self::$name,
            'source_id' => $sourceId,
          ));

          $userProfile->content = sfYaml::dump($this->getUserContent($response));
        }
      }
    }

    return $userProfile;
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

  public function getCookieName()
  {
  }
}