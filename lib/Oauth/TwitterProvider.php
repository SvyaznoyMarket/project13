<?php

namespace Oauth;

class TwitterProvider implements ProviderInterface {
    const NAME = 'twitter';

    /** @var \Config\Oauth\TwitterConfig */
    private $config;
    /** @var  $oauth_token_secret */
    private $oauth_token_secret;

    /**
     * @param \Config\Oauth\TwitterConfig $config
     */
    public function __construct(\Config\Oauth\TwitterConfig $config) {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    private function generate_base_url_signature()
    {
        $return = [];

        // рандомная строка (для безопасности)
        $return['oauth_nonce'] = md5(uniqid(rand(), true));
        // ae058c443ef60f0fea73f10a89104eb9

        // время когда будет выполняться запрос (в секундых)
        $return['oauth_timestamp'] = time();
         // 1310727371

        /**
         * Обращаем внимание на использование функции urlencode и расположение амперсандов.
         * Если поменяете положение параметров oauth_... или уберете где-нибудь urlencode - получите ошибку
         */
        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode('https://api.twitter.com/oauth/request_token')."&";
        $oauth_base_text .= urlencode("oauth_callback=".urlencode(\App::router()->generateUrl('user.login.external.response', ['providerName' => self::NAME], true))."&");
        $oauth_base_text .= urlencode("oauth_consumer_key=".$this->config->clientId."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$return['oauth_nonce']."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$return['oauth_timestamp']."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $key = $this->config->secretKey."&"; // На конце должен быть амперсанд & !!!
        // key: OYUjBgJPl4yra3N32sSpSSVGboLSCo5pLGsky20VJE&

        $return['oauth_signature'] = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
        // oauth_signature: BB6w/jAdrHQD1/iUqqEZiI8o2M0=

        $url = 'https://api.twitter.com/oauth/request_token';
        $url .= '?oauth_callback='.urlencode(\App::router()->generateUrl('user.login.external.response', ['providerName' => self::NAME], true));
        $url .= '&oauth_consumer_key='.$this->config->clientId;
        $url .= '&oauth_nonce='.$return['oauth_nonce'];
        $url .= '&oauth_signature='.urlencode($return['oauth_signature']);
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$return['oauth_timestamp'];
        $url .= '&oauth_version=1.0';

        $response = file_get_contents($url);
        parse_str($response, $result);

        return $result;
    }

    /**
     * @param string $oauth_token
     * @param string $oauth_verifier
     */
    private function generate_access_url_signature($oauth_token = '', $oauth_verifier= '')
    {
        $return = [];
        // рандомная строка (для безопасности)
        $return['oauth_nonce'] = md5(uniqid(rand(), true));
        // ae058c443ef60f0fea73f10a89104eb9

        // время когда будет выполняться запрос (в секундых)
        $return['oauth_timestamp'] = time();
        // 1310727371

        $return['oauth_token'] = $oauth_token;
        $return['oauth_verifier'] = $oauth_verifier;

        $return['oauth_token_secret']= $this->oauth_token_secret;

        /**
         * Обратите внимание на использование функции urlencode и расположение амперсандов.
         * Если поменяете положение параметров oauth_... или уберете где-нибудь urlencode - получите ошибку
         *
         */
        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode('https://api.twitter.com/oauth/access_token')."&";
        $oauth_base_text .= urlencode("oauth_consumer_key=".$this->config->clientId."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$return['oauth_nonce']."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_token=".$return['oauth_token']."&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$return['oauth_timestamp']."&");
        $oauth_base_text .= urlencode("oauth_verifier=".$return['oauth_verifier']."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $key = $this->config->secretKey."&".$return['oauth_token_secret'];

        $return['oauth_signature'] = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $url = 'https://api.twitter.com/oauth/access_token';
        $url .= '?oauth_nonce='.$return['oauth_nonce'];
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$return['oauth_timestamp'];
        $url .= '&oauth_consumer_key='.$this->config->clientId;
        $url .= '&oauth_token='.urlencode($return['oauth_token']);
        $url .= '&oauth_verifier='.urlencode($return['oauth_verifier']);
        $url .= '&oauth_signature='.urlencode($return['oauth_signature']);
        $url .= '&oauth_version=1.0';

        $response = file_get_contents($url);
        parse_str($response, $result);

        return $result;
    }

    /**
     * @param string $oauth_token
     * @param string $screen_name
     * @return string
     */
    private function generate_access_profile_signature($oauth_token = '',$oauth_token_secret='', $screen_name='')
    {
        $return = [];
        // рандомная строка (для безопасности)
        $return['oauth_nonce'] = md5(uniqid(rand(), true));
        // ae058c443ef60f0fea73f10a89104eb9

        // время когда будет выполняться запрос (в секундых)
        $return['oauth_timestamp'] = time();
        // 1310727371

        $return['oauth_token'] = $oauth_token;
        //$return['oauth_verifier'] = $oauth_verifier;

        $return['oauth_token_secret'] = $oauth_token_secret;

        $return['screen_name'] = $screen_name;

        /**
         * Обратите внимание на использование функции urlencode и расположение амперсандов.
         * Если поменяете положение параметров oauth_... или уберете где-нибудь urlencode - получите ошибку
         *
         */
        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode('https://api.twitter.com/1.1/users/show.json').'&';
        $oauth_base_text .= urlencode('oauth_consumer_key='.$this->config->clientId.'&');
        $oauth_base_text .= urlencode('oauth_nonce='.$return['oauth_nonce'].'&');
        $oauth_base_text .= urlencode('oauth_signature_method=HMAC-SHA1&');
        $oauth_base_text .= urlencode('oauth_timestamp='.$return['oauth_timestamp']."&");
        $oauth_base_text .= urlencode('oauth_token='.$return['oauth_token']."&");
        $oauth_base_text .= urlencode('oauth_version=1.0&');
        $oauth_base_text .= urlencode('screen_name=' . $return['screen_name']);

        $key = $this->config->secretKey . '&' . $return['oauth_token_secret'];
        $return['signature'] = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        // Формируем GET-запрос
        $url = 'https://api.twitter.com/1.1/users/show.json';
        $url .= '?oauth_consumer_key=' . $this->config->clientId;
        $url .= '&oauth_nonce=' . $return['oauth_nonce'];
        $url .= '&oauth_signature=' . urlencode($return['signature']);
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp=' . $return['oauth_timestamp'];
        $url .= '&oauth_token=' . urlencode($return['oauth_token']);
        $url .= '&oauth_version=1.0';
        $url .= '&screen_name=' . $return['screen_name'];

        return $url;
    }

    /**
     * @return string
     */
    public function getLoginUrl() {
        $base = $this->generate_base_url_signature();
        $this->oauth_token_secret = $base[oauth_token_secret];

        return 'https://api.twitter.com/oauth/authorize?' . http_build_query([
            'oauth_token'     => $base[oauth_token]
        ]);
    }

    /**
     * @param \Http\Request $request
     * @return \Oauth\Model\Vkontakte\Entity|null
     */
    public function getUser(\Http\Request $request) {
        //$code = $request->get('code');
        $oauth_token = $request->get('oauth_token');
        $oauth_verifier = $request->get('oauth_verifier');


        if (empty($oauth_token) || empty($oauth_verifier)) {
            \App::logger()->warn(['provider' => self::NAME, 'request' => $request->query->all()], ['oauth']);
            return null;
        }

        $response = $this->generate_access_url_signature($oauth_token, $oauth_verifier);
        //$response = $this->query($this->getAccessTokenUrl($return));
        if (empty($response['oauth_token']) || empty($response['oauth_token_secret']) || empty($response['user_id'])) {
            \App::logger()->warn(['provider' => self::NAME, 'response' => $response], ['oauth']);
            return null;
        }
        $userId = $response['id_str'];

        $url = $this->generate_access_profile_signature($response['oauth_token'], $response['oauth_token_secret'], $response['screen_name']);
        // делаем запрос
        $response = $this->query($url);

        // разбираем запрос

        if (empty($response['id_str']) || empty($response['name']) || ('DELETED' == $response['name'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $url, 'response' => $response], ['oauth']);
            return null;
        }

        $user = new \Oauth\Model\Twitter\Entity($response);

        return $user;
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
