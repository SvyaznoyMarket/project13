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

} 