<?php

namespace Oauth;

class OdnoklassnikiProvider implements ProviderInterface {
    const NAME = 'odnoklassniki';

    /** @var \Config\Oauth\OdnoklassnikiConfig */
    private $config;

    /**
     * @param \Config\Oauth\OdnoklassnikiConfig $config
     */
    public function __construct(\Config\Oauth\OdnoklassnikiConfig $config) {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLoginUrl() {
        return 'http://www.odnoklassniki.ru/oauth/authorize?' . http_build_query([
            'client_id'     => $this->config->clientId,
            'redirect_uri'  => \App::router()->generateUrl('user.login.external.response', ['providerName' => self::NAME], true),
            'response_type' => 'code',
            'scope' => 'VALUABLE_ACCESS;SET_STATUS'
        ]);
    }

    /**
     * @param \Http\Request $request
     * @return \Oauth\Model\Odnoklassniki\Entity|null
     */
    public function getUser(\Http\Request $request) {
        $code = $request->get('code');

        if (empty($code)) {
            \App::logger()->warn(['provider' => self::NAME, 'request' => $request->query->all()], ['oauth']);
            return null;
        }

        $response = $this->queryPost($this->getAccessTokenUrl(),[
            'code'          => $code,
            'redirect_uri'  => \App::router()->generateUrl('user.login.external.response', ['providerName' => self::NAME], true),
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->config->clientId,
            'client_secret' => $this->config->secretKey,
        ]);

        if (empty($response['access_token'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $this->getAccessTokenUrl($code), 'response' => $response], ['oauth']);
            return null;
        }
        $accessToken = $response['access_token'];

        $response = $this->query($this->getProfileUrl($accessToken));

        if (empty($response['uid']) || empty($response['first_name'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $this->getProfileUrl($accessToken), 'response' => $response], ['oauth']);
            return null;
        }

        $user = new \Oauth\Model\Odnoklassniki\Entity($response);

        return $user;
    }

    /**
     * @return string
     */
    private function getAccessTokenUrl() {
        return 'http://api.odnoklassniki.ru/oauth/token.do?';
    }

    /**
     * @param string $accessToken
     * @return string
     */
    private function getProfileUrl($accessToken) {
        $sig = md5('application_key=' . $this->config->publicKey . 'method=users.getCurrentUser' . md5($accessToken . $this->config->secretKey));

        return 'http://api.odnoklassniki.ru/fb.do?method=users.getCurrentUser&' . http_build_query([
            'access_token'    => $accessToken,
            'application_key' => $this->config->publicKey,
            'sig'             => $sig,
        ]);
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed|null
     * @throws \Exception
     */
    private function queryPost($url, array $data = []) {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url); // url, куда будет отправлен запрос
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($data))); // передаём параметры
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 5);

            $result = curl_exec($curl);

            curl_close($curl);

            $response = json_decode($result, true);

            if (!$response || !empty($response['error'])) {
                \App::logger()->warn(['provider' => self::NAME, 'url' => $url, 'data' => $data, 'response' => $response], ['oauth']);
                throw new \Exception($response['error'] . (isset($response['error_description']) ? (': ' . $response['error_description']) : ''));
            }
        } catch (\Exception $e) {
            $response = null;
            \App::logger()->error($e, ['oauth']);
        }

        return $response;
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed|null
     * @throws \Exception
     */
    private function query($url, array $data = []) {
        $client = new \Curl\Client(\App::logger());

        try {
            $response = $client->query($url, $data);

            //$response = json_decode($response, true);

            // TODO: json_last_error()

            if (!$response || !empty($response['error'])) {
                \App::logger()->warn(['provider' => self::NAME, 'url' => $url, 'data' => $data, 'response' => $response], ['oauth']);
                throw new \Exception($response['error'] . (isset($response['error_description']) ? (': ' . $response['error_description']) : ''));
            }
        } catch (\Exception $e) {
            $response = null;
            \App::logger()->error($e, ['oauth']);
        }

        return $response;
    }
}