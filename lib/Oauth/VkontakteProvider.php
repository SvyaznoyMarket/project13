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
     * @param string $redirect_to
     * @param string $subscribe "1" - если пользователь подписался на рассылку, пустая строка в противном случае
     * @return string
     */
    public function getLoginUrl($redirect_to = '', $subscribe = '') {
        return 'https://oauth.vk.com/authorize?' . http_build_query([
            'client_id'     => $this->config->clientId,
            'scope'         => 'email',//, offline для получения токена без срока годности
            'redirect_uri'  => \App::router()->generateUrl('user.login.external.response', ['providerName' => self::NAME, 'subscribe' => $subscribe, 'redirect_to' => $redirect_to], true),
            'response_type' => 'code'
        ]);
    }

    /**
     * @param \Http\Request $request
     * @return \Oauth\Model\Vkontakte\Entity|null
     */
    public function getUser(\Http\Request $request) {

        $code = $request->get('code');
        if (empty($code)) {
            \App::logger()->warn(['provider' => self::NAME, 'request' => $request->query->all()], ['oauth']);
            return null;
        }

        $response = $this->query($this->getAccessTokenUrl($code, $request->get('redirect_to'), $request->get('subscribe')));


        if (empty($response['access_token']) || empty($response['user_id'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $this->getAccessTokenUrl($code, $request->get('redirect_to'), $request->get('subscribe')), 'response' => $response], ['oauth']);
            return null;
        }
        $userId = $response['user_id'];
        $access_token = $response['access_token'];
        $email = $response['email'];

        $response = $this->query($this->getProfileUrl($userId));

        $response = (isset($response['response']) && is_array($response['response'])) ? reset($response['response']) : [];
        if (empty($response['uid']) || empty($response['first_name']) || ('DELETED' == $response['first_name'])) {
            \App::logger()->warn(['provider' => self::NAME, 'url' => $this->getProfileUrl($userId), 'response' => $response], ['oauth']);
            return null;
        }

        $user = new \Oauth\Model\Vkontakte\Entity($response);
        $user->setAccessToken($access_token);
        $user->setEmail($email);

        return $user;
    }

    /**
     * @param $code
     * @param string $redirect_to
     * @param string $subscribe "1" - если пользователь подписался на рассылку, пустая строка в противном случае
     * @return string
     */
    private function getAccessTokenUrl($code, $redirect_to = '', $subscribe = '') {
        return 'https://oauth.vk.com/access_token?' . http_build_query([
            'client_id'     => $this->config->clientId,
            'client_secret' => $this->config->secretKey,
            'code'          => $code,
            'redirect_uri'  => \App::router()->generateUrl('user.login.external.response', ['providerName' => self::NAME, 'subscribe' => $subscribe, 'redirect_to' => $redirect_to], true),
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    private function getProfileUrl($id) {
        return 'https://api.vk.com/method/users.get?' . http_build_query([
            'uids'   => $id,
            'fields' => 'uid,contacts,education,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big',
        ]);
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

            //возвращается массив с данными не JSON
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