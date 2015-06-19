<?php


namespace Controller\OrderV3;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class OrderV3 {
    use CurlTrait;

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
                    'products' => array_map(function ($item) {
                        return [
                            'uid'      => $item['ui'],
                            'quantity' => $item['quantity'],
                        ];
                    }, $cart->getProductData()
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
} 