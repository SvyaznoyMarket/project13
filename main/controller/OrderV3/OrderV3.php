<?php


namespace Controller\OrderV3;


class OrderV3 {

    /** Флаг первичного просмотра страницы */
    const SESSION_IS_READED_KEY = 'orderV3_is_readed';

    /** @var \Core\ClientV2 */
    protected $client;
    /** @var \Session\User */
    protected $user;
    /** @var \Session\Cart|\Session\Cart\OneClick */
    protected $cart;
    /** @var \Http\Session */
    protected $session;
    /** @var  string */
    protected $splitSessionKey;

    public function __construct() {
        $this->session = \App::session();
        $this->splitSessionKey = \App::config()->order['splitSessionKey'];
        $this->client = \App::coreClientV2();
        $this->user = \App::user();
        $this->cart = in_array(\App::request()->attributes->get('route'), ['orderV3.one-click', 'orderV3.delivery.one-click']) ? $this->cart = $this->user->getOneClickCart() : $this->user->getCart();
    }

    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->debug) return null; // чтобы можно было смотреть разбиение на тестовых площадках

        if (!in_array($this->user->getRegion()->getId(), [119623, 93746, 14974])) {
            return new \Http\RedirectResponse(\App::router()->generate('order'));
        }

        return null;
    }

    public function logger($data) {

        if (!is_array($data)) $data = array($data);

        $sessionData = \App::session()->get($this->splitSessionKey);

        $userPhone = ((bool)$sessionData && isset($sessionData['user_info']['phone']))
            ? \App::session()->get($this->splitSessionKey)['user_info']['phone']
            : '';

        $commonData = [
            'sessionId' => $this->session->getId(),
            'userAuth' => $this->user->getEntity() !== null,
            'regionId' => $this->user->getRegion()->getId(),
            'time' =>strftime('%Y-%m-%d %H:%M:%S'),
            'userPhone' => $userPhone
        ];

        \App::logger('custom')->info(['data' => array_merge($commonData, $data)], ['order-v3-log']);
    }

    public function logFromWeb(\Http\Request $request) {
        $this->logger($request->request->all());
        return new \Http\JsonResponse();
    }

} 