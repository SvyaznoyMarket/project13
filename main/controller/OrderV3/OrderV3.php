<?php


namespace Controller\OrderV3;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class OrderV3 {
    use CurlTrait;

    /** Флаг первичного просмотра страницы */
    const SESSION_IS_READED_KEY = 'orderV3_is_readed';
    const SESSION_IS_READED_AFTER_ALL_ONLINE_ORDERS_ARE_PAID_KEY = 'orderV3_is_readed_after_all_online_orders_are_paid';

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

    public function execute(\Http\Request $request) {
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

    /**
     * @param array $data
     */
    protected function pushEvent(array $data) {
        try {
            $sessionData = (array)$this->session->get($this->splitSessionKey) + [
                    'orders'    => [],
                    'user_info' => [],
                ];

            $userInfo = (array)$sessionData['user_info'] + [
                    'email'      => null,
                    'phone'      => null,
                    'first_name' => null,
                ];

            $userEntity = \App::user()->getEntity();
            $cart = \App::user()->getCart();

            $data = array_replace_recursive([
                'step'        => null,
                'user'        => [
                    'uid'   => $userEntity ? $userEntity->getUi() : null,
                    'email' => $userInfo['email'],
                    'phone' => $userInfo['phone'],
                    'name'  => $userInfo['first_name'],
                ],
                'session_id'  => \App::session()->getId(),
                'cart'        => [
                    'products' => array_map(
                        function (\Model\Cart\Product\Entity $cartProduct) {
                            return [
                                'uid'      => $cartProduct->ui,
                                'quantity' => $cartProduct->quantity,
                            ];
                        },
                        $cart->getProductsById()
                    ),
                    'sum'      => $cart->getSum(),
                ],
                'order_count' => isset($sessionData['orders']) ? count($sessionData['orders']) : null,
            ], $data);
            (new Query\Event\PushOrderStep($data))->prepare();

            $this->getCurl()->execute();
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e]);
        }
    }

    /**
     * Валидация пользовательских данных ядром
     * Метод вынесен из cart/split для облегчения
     * @link https://wiki.enter.ru/pages/viewpage.action?pageId=25869011
     * @param   $data   []
     * @return  mixed
     */
    public function validateUserInfo($data) {

        $userData = [
            'phone'             => '',
            'first_name'        => '',
            'last_name'         => '',
            'email'             => '',
            'address_id'        => '',
            'bonus_card_number' => '',
            'address'       => [
                'street'        => '',
                'building'      => '',
                'number'        => '',
                'apartment'     => '',
                'floor'         => '',
                'metro_station' => '',
                'kladr_id'      => ''
            ]
        ];

        return $this->client->query('cart/validate-user-info', [], array_merge($userData, $data));

    }

    /**
     * Есть ли товары не от Enter?
     * @return bool
     */
    protected function hasProductsOnlyFromPartner() {
        foreach ($this->cart->getProductsById() as $cartProduct) {
            if ($cartProduct->isOnlyFromPartner) {
                return true;
            }
        }

        return false;
    }
} 