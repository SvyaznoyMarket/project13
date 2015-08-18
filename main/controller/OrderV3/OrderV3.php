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
        if (in_array(\App::request()->attributes->get('route'), ['order.oneClick.new', 'orderV3.one-click', 'orderV3.delivery.one-click'], true)) {
            $sessionData = \App::session()->get('user/cart/one-click');
            \App::session()->remove('user/cart/one-click');
            if (!isset($sessionData['product']) || !is_array($sessionData['product']) || count($sessionData['product']) != 1) {
                throw new \Exception\NotFoundException('Для наборов одноклик не работает');
            }

            $productId = (int)key($sessionData['product']);

            if (!$productId) {
                throw new \Exception\NotFoundException('Товар не найден');
            }

            /** @var \Model\Product\Entity[] $products */
            $products = [new \Model\Product\Entity(['id' => $productId])];
            \RepositoryManager::product()->useV3()->withoutModels()->prepareProductQueries($products);
            \App::coreClientV2()->execute();

            if (!$products) {
                throw new \Exception\NotFoundException('Товар не найден');
            }

            $params = [
                'productPath' => $products[0]->getPath(),
            ];

            $sessionProduct = reset($sessionData['product']);

            if ($sessionProduct['sender']) {
                $params['sender'] = $sessionProduct['sender'];
            }

            if ($sessionProduct['sender2']) {
                $params['sender2'] = $sessionProduct['sender2'];
            }

            return new \Http\RedirectResponse(\App::router()->generate('product', $params) . '#one-click' . (isset($sessionData['shop']) && $sessionData['shop'] ? '-' . $sessionData['shop'] : ''));
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
                    'products' => array_map(
                        function (\Model\Cart\Product\Entity $cartProduct) {
                            return [
                                'uid'      => $cartProduct->ui,
                                'quantity' => $cartProduct->quantity,
                            ];
                        },
                        $cart->getInOrderProductsById()
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