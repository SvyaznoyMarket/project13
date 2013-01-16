<?php

namespace Oauth;

class VkontakteProvider implements ProviderInterface {
    const NAME = 'vkontakte';

    /** @var \Config\Oauth\VkontakteConfig */
    private $config;

    /**
     * @param \Config\Oauth\VkontakteConfig $config
     */
    public function __construct(\Config\Oauth\VkontakteConfig $config) {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLoginUrl() {
        return 'http://oauth.vk.com/authorize?' . http_build_query(array(
            'client_id'     => $this->config->clientId,
            'scope'         => '',
            'redirect_uri'  => \App::router()->generate('user.login.external.response', array('providerName' => self::NAME), true),
            'response_type' => 'code',
        ));
    }

    /**
     * @param \Http\Request $request
     * @return \Oauth\Model\Vkontakte\Entity|null
     */
    public function getUser(\Http\Request $request) {
        $code = $request->get('code');

        if (empty($code)) {
            \App::logger()->warn(array('provider' => self::NAME, 'request' => $request->query->all()));
            return null;
        }

        $response = $this->query($this->getAccessTokenUrl($code));
        if (empty($response['access_token']) || empty($response['user_id'])) {
            \App::logger()->warn(array('provider' => self::NAME, 'url' => $this->getAccessTokenUrl($code), 'response' => $response));
            return null;
        }
        $userId = $response['user_id'];

        $response = $this->query($this->getProfileUrl($userId));
        $response = (isset($response['response']) && is_array($response['response'])) ? reset($response['response']) : array();
        if (empty($response['uid']) || empty($response['first_name']) || ('DELETED' == $response['first_name'])) {
            \App::logger()->warn(array('provider' => self::NAME, 'url' => $this->getProfileUrl($userId), 'response' => $response));
            return null;
        }

        $user = new \Oauth\Model\Vkontakte\Entity($response);

        return $user;
    }

    /**
     * @param string $code
     * @return string
     */
    private function getAccessTokenUrl($code) {
        return 'https://oauth.vk.com/access_token?' . http_build_query(array(
            'client_id'     => $this->config->clientId,
            'client_secret' => $this->config->secretKey,
            'code'          => $code,
            'redirect_uri'  => \App::router()->generate('user.login.external.response', array('providerName' => self::NAME), true),
        ));
    }

    /**
     * @param $id
     * @return string
     */
    private function getProfileUrl($id) {
        return 'https://api.vk.com/method/users.get?' . http_build_query(array(
            'uids'   => $id,
            'fields' => 'uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big',
        ));
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed|null
     * @throws \Exception
     */
    private function query($url, array $data = array()) {
        $client = new \Curl\Client();

        try {
            $response = $client->query($url, $data);
            $response = json_decode($response, true);

            // TODO: json_last_error()

            if (!$response || !empty($response['error'])) {
                \App::logger()->warn(array('provider' => self::NAME, 'url' => $url, 'data' => $data, 'response' => $response));
                throw new \Exception($response['error'] . (isset($response['error_description']) ? (': ' . $response['error_description']) : ''));
            }
        } catch (\Exception $e) {
            $response = null;
            \App::logger()->error($e);
        }

        return $response;
    }
}