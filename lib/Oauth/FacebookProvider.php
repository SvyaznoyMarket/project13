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
     * @param string $redirect_to
     * @param string $subscribe "1" - если пользователь подписался на рассылку, пустая строка в противном случае
     * @return string
     */
    public function getLoginUrl($redirect_to = '', $subscribe = '') {
        return 'https://www.facebook.com/dialog/oauth?' . http_build_query([
            'client_id'     => $this->config->clientId,
            'redirect_uri'  => \App::router()->generateUrl('user.login.external.response', ['providerName' => self::NAME, 'subscribe' => $subscribe, 'redirect_to' => $redirect_to], true),
            'response_type' => 'code',
            'scope'         => 'email,user_birthday'
        ]);
    }

    /**
     * @param \Http\Request $request
     * @return \Oauth\Model\Facebook\Entity|null
     */
    public function getUser(\Http\Request $request) {
        $code = $request->get('code');

        if (empty($code)) {
            \App::logger()->warn(['provider' => self::NAME, 'request' => $request->query->all()], ['oauth']);
            return null;
        }

        $response = $this->query($this->getAccessTokenUrl($code, $request->get('redirect_to'), $request->get('subscribe')), [], false, true);

        parse_str($response, $response);

        if (empty($response['access_token'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $this->getAccessTokenUrl($code, $request->get('redirect_to'), $request->get('subscribe')), 'response' => $response], ['oauth']);
            return null;
        }
        $accessToken = $response['access_token'];

        $response = $this->query($this->getProfileUrl($accessToken), [], false, false);

        $response = is_array($response) ? $response : [];
        if (empty($response['id']) || empty($response['first_name'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $this->getProfileUrl($accessToken), 'response' => $response], ['oauth']);
            return null;
        }

        $user = new \Oauth\Model\Facebook\Entity($response);
        $user->setAccessToken($accessToken);

        return $user;
    }

    /**
     * @param $code
     * @param string $redirect_to
     * @param string $subscribe "1" - если пользователь подписался на рассылку, пустая строка в противном случае
     * @return string
     */
    private function getAccessTokenUrl($code, $redirect_to = '', $subscribe = '') {
        return 'https://graph.facebook.com/oauth/access_token?' . http_build_query([
            'client_id'     => $this->config->clientId,
            'redirect_uri'  => \App::router()->generateUrl('user.login.external.response', ['providerName' => self::NAME, 'subscribe' => $subscribe, 'redirect_to' => $redirect_to], true),
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
     * @param bool $get_access
     * @return mixed|null
     * @throws \Exception
     */
    private function query($url, array $data = [], $jsonDecode = true, $get_access = false) {
        $client = new \Curl\Client(\App::logger());

        try {
            //
            // делаем запрос
            if($get_access){
                $response = file_get_contents($url);
            }
            else {
                $response = $client->query($url, $data);
            }

            if ($jsonDecode) {
                $response = json_decode($response, true);

                // TODO: json_last_error()

                if (!$response || !empty($response['error']['code'])) {
                    \App::logger()->warn(['provider' => self::NAME, 'url' => $url, 'data' => $data, 'response' => $response], ['oauth']);
                    throw new \Exception($response['error']['code'] . (isset($response['error']['message']) ? (': ' . $response['error']['message']) : ''));
                }
            }
        } catch (\Exception $e) {
            $response = null;
            \App::logger()->error($e, ['oauth']);
        }

        return $response;
    }
}