<?php


namespace Controller\OrderV3;


class OrderV3 {

    /** @var \Core\ClientV2 */
    protected $client;
    /** @var \Session\User */
    protected $user;
    /** @var \Session\Cart */
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
        $this->cart = $this->user->getCart();
    }

    public function logger($data) {

        if (!is_array($data)) $data = array($data);

        $commonData = [
            'sessionId' => $this->session->getId(),
            'userAuth' => $this->user->getEntity() !== null,
            'regionId' => $this->user->getRegionId(),
            'time' =>strftime('%Y-%m-%d %H:%M:%S')
        ];

        \App::logger()->info(['data' => array_merge($commonData, $data)], ['order-v3-log']);
    }

    public function logFromWeb(\Http\Request $request) {
        $this->logger($request->request->all());
        return new \Http\JsonResponse();
    }

} 