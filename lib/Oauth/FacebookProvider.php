<?php

namespace Oauth;

class FacebookProvider implements ProviderInterface {
    const NAME = 'facebook';

    /** @var \Config\Oauth\FacebookConfig */
    private $config;

    /**
     * @param \Config\Oauth\FacebookConfig $config
     */
    public function __construct(\Config\Oauth\FacebookConfig $config) {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLoginUrl() {
        return 'https://www.facebook.com/dialog/oauth?' . http_build_query([
            'client_id'     => $this->config->clientId,
            'redirect_uri'  => \App::router()->generate('user.login.external.response', ['providerName' => self::NAME], true),
            'response_type' => 'code',
        ]);
    }

    /**
     * @param \Http\Request $request
     * @return \Oauth\Model\Facebook\Entity|null
     */
    public function getUser(\Http\Request $request) {
        $code = $request->get('code');

        if (empty($code)) {
            \App::logger()->warn(['provider' => self::NAME, 'request' => $request->query->all()]);
            return null;
        }

        $response = $this->query($this->getAccessTokenUrl($code), [], false);
        parse_str($response, $response);
        if (empty($response['access_token'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $this->getAccessTokenUrl($code), 'response' => $response]);
            return null;
        }
        $accessToken = $response['access_token'];

        $response = $this->query($this->getProfileUrl($accessToken));
        $response = is_array($response) ? $response : [];
        if (empty($response['id']) || empty($response['first_name'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $this->getProfileUrl($accessToken), 'response' => $response]);
            return null;
        }

        $user = new \Oauth\Model\Facebook\Entity($response);

        return $user;
    }

    /**
     * @param string $code
     * @return string
     */
    private function getAccessTokenUrl($code) {
        return 'https://graph.facebook.com/oauth/access_token?' . http_build_query([
            'client_id'     => $this->config->clientId,
            'redirect_uri'  => \App::router()->generate('user.login.external.response', ['providerName' => self::NAME], true),
            'client_secret' => $this->config->secretKey,
            'code'          => $code,
        ]);
    }

    /**
     * @param $accessToken
     * @return string
     */
    private function getProfileUrl($accessToken) {
        return 'https://graph.facebook.com/me?' . http_build_query([
            'access_token' => $accessToken,
        ]);
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $jsonDecode
     * @return mixed|null
     * @throws \Exception
     */
    private function query($url, array $data = [], $jsonDecode = true) {
        $client = new \Curl\Client(\App::logger());

        try {
            $response = $client->query($url, $data);
            if ($jsonDecode) {
                $response = json_decode($response, true);

                // TODO: json_last_error()

                if (!$response || !empty($response['error']['code'])) {
                    \App::logger()->warn(['provider' => self::NAME, 'url' => $url, 'data' => $data, 'response' => $response]);
                    throw new \Exception($response['error']['code'] . (isset($response['error']['message']) ? (': ' . $response['error']['message']) : ''));
                }
            }
        } catch (\Exception $e) {
            $response = null;
            \App::logger()->error($e);
        }

        return $response;
    }
}