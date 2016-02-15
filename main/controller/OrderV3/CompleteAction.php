<?php

namespace Controller\OrderV3;

use EnterApplication\CurlTrait;
use Session\AbTest\ABHelperTrait;
use Model\Order\Entity;
use Model\Order\Product\Entity as Product;
use Model\PaymentMethod\PaymentEntity;
use Model\Point\PointEntity;
use Session\ProductPageSenders;
use Session\ProductPageSenders2;
use EnterQuery as Query;

class CompleteAction extends OrderV3 {
    use CurlTrait, ABHelperTrait;

    private $sessionOrders;
    private $sessionIsReaded;
    private $sessionIsReadedAfterAllOnlineOrdersArePaid;

    public function __construct() {
        parent::__construct();
        $this->sessionOrders = $this->session->get(\App::config()->order['sessionName'] ? : 'lastOrder');
        $this->sessionIsReaded = $this->session->get(self::SESSION_IS_READED_KEY);
        $this->sessionIsReadedAfterAllOnlineOrdersArePaid = $this->session->get(self::SESSION_IS_READED_AFTER_ALL_ONLINE_ORDERS_ARE_PAID_KEY);
    }

    /**
     * @param $request \Http\Request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {

        $page = new \View\OrderV3\CompletePage();
        //\App::logger()->debug('Exec ' . __METHOD__);

        ProductPageSenders::clean();
        ProductPageSenders2::clean();

        $context = $request->get('context');

        /** @var \Model\Order\Entity[] $orders */
        $orders = [];
        /** @var \Model\PaymentMethod\PaymentEntity[] $ordersPayment */
        $ordersPayment = [];
        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $paymentProviders = [];
        $privateClient = \App::coreClientPrivate();
        $needCreditBanksData = false;
        /** @var $banks \Model\CreditBank\Entity[] */
        $banks = [];
        $shopIds = [];
        $pointUis = [];
        $errors = [];
        $userEntity = \App::user()->getEntity();

        $this->pushEvent(['step' => 3]);

        if ($context) {
            $now = new \DateTime();
            $this->client->addQuery(
                'order/get-context',
                [
                    'hash' => $context,
                ],
                [],
                function($data) use (&$now) {
                    foreach ($data as $item) {
                        $number = isset($item['order']['number']) ? $item['order']['number'] : null;
                        if (!$number) continue;

                        $this->sessionOrders[$number] = $item['order'];
                    }
                },
                function(\Exception $e) {
                    \App::exception()->remove($e);

                    \App::logger()->error(['error' => $e, 'message' => 'Заказы не найдены', 'sender' => __FILE__ . ' ' .  __LINE__], ['order', 'fatal']);
                }
            );
            $this->client->execute();
        }

        try {

            if (!(bool)$this->sessionOrders) throw new \Exception('В сессии нет заказов');

            if ($request->query->has('svyaznoyClub')) {
                $activateSuccess = $this->completeSvyaznoy($request, $this->sessionOrders, $page);
                if ($activateSuccess) return new \Http\RedirectResponse(\App::router()->generate('orderV3.complete', ['refresh' => 1]));
            }

            // забираем заказы и доступные методы оплаты
            foreach ($this->sessionOrders as $sessionOrder) {

                if (!isset($sessionOrder['number'])) continue;

                // сами заказы
                if (('call-center' !== $this->session->get(\App::config()->order['channelSessionKey'])) && \App::config()->order['sessionInfoOnComplete'] && !$request->query->get('refresh')) { // SITE-4828
                    $orders[$sessionOrder['number']] = new Entity($sessionOrder);
                } else {
                    $this->client->addQuery('order/get-by-mobile', ['number' => $sessionOrder['number'], 'mobile' => preg_replace('/[^0-9]/', '', $sessionOrder['mobile'])], [], function ($data) use (&$orders, $sessionOrder) {
                        $data = reset($data);
                        $orders[$sessionOrder['number']] = $data ? new Entity($data) : null;
                    });
                }

                // методы оплаты для заказа
                $this->client->addQuery(
                    'payment-method/get-for-order',
                    [
                        'geo_id'     => $this->user->getRegion()->getId(),
                        'client_id'  => 'site',
                        'number_erp' => $sessionOrder['number_erp']
                    ],
                    [],
                    function ($data) use ($sessionOrder, &$ordersPayment) {
                        $ordersPayment[$sessionOrder['number']] = new PaymentEntity($data);
                    },
                    function (\Exception $e) {
                        \App::logger()->error(['error' => $e, 'message' => 'Не получены методы оплаты', 'sender' => __FILE__ . ' ' .  __LINE__], ['order']);
                        \App::exception()->remove($e);
                    }
                );
            }

            unset($sessionOrder);

            $this->client->execute();

            // изменяет order.pauSum, если есть акция
            foreach ($ordersPayment as $orderNumber => $payment) {
                if (!$order = $orders[$orderNumber]) continue;

                if ($order->paymentId && ($sum = $payment->getPaymentSumByMethodId($order->paymentId))) {
                    $order->paySum = $sum;
                }
            }

            // получаем продукты для заказов
            foreach ($orders as $order) {
                // TODO все данные заказываемых товаров необходимо сохранять в сессии на первом шаге оформления заказа, т.к. на последнем шаге товара уже может не быть в бэкэнде или он будет заблокирован 
                foreach ($order->getProduct() as $product) {
                    $products[$product->getId()] = new \Model\Product\Entity(['id' => $product->getId()]);
                }
                
                \RepositoryManager::product()->prepareProductQueries($products, 'media category brand label');

                // Нужны ли нам кредитные банки?
                if ($order->isCredit()) $needCreditBanksData = true;
                // и магазины
                if ($order->getShopId()) $shopIds[$order->getShopId()] = $order->getNumber();
                if ($order->getDelivery()->pointUi) $pointUis[$order->getDelivery()->pointUi] = $order->getNumber();

            }

            // Запрашиваем данные по кредитным банкам
            if ($needCreditBanksData) {
                \RepositoryManager::creditBank()->prepareCollection(function($data) use (&$banks){
                    foreach ($data as $item) {
                        if (isset($item['token'])) $banks[$item['token']] = new \Model\CreditBank\Entity($item);
                    }
                });
            }

            // Запрашиваем магазины (они пока нужны для получения для ссылки "Как добраться")
            if (!empty($shopIds)) \RepositoryManager::shop()->prepareCollectionById(array_keys($shopIds), function($data) use ($shopIds, &$orders) {
                foreach ((array)$data as $shopData) {
                    $orders[$shopIds[$shopData['id']]]->shop = new \Model\Shop\Entity($shopData);
                }
            });

            // Запрашиваем точки
            if (!empty($pointUis)) \RepositoryManager::shop()->preparePointCollectionByUi(array_keys($pointUis), function($data) use ($pointUis, &$orders) {
                foreach ((array)$data as $point) {
                    if (isset($orders[$pointUis[$point['ui']]])) $orders[$pointUis[$point['ui']]]->point = new PointEntity($point);
                }
            });

            $this->client->execute();
            $privateClient->execute();
            unset($order, $methodId, $onlineMethodsId, $privateClient, $needCreditBanksData);

            // очищаем корзину от заказанных продуктов
            $updateResultProducts = [];
            try {
                $updateResultProducts = $this->cart->update(array_map(function(\Model\Product\Entity $product){ return ['ui' => $product->ui, 'quantity' => 0]; }, $products));
            } catch(\Exception $e) {}

            if ($userEntity && $this->isCoreCart()) {
                try {
                    foreach ($updateResultProducts as $updateResultProduct) {
                        if ($updateResultProduct->setAction === 'delete') {
                            (new Query\Cart\RemoveProduct($userEntity->getUi(), $updateResultProduct->cartProduct->ui))->prepare();
                        }
                    }
                    $this->getCurl()->execute();
                } catch (\Exception $e) {
                    \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' . __LINE__], ['order']);
                }
            }

            // собираем статистику для RichRelevance
            foreach ($orders as $order) {
                try {
                    if (\App::config()->product['pushRecommendation']) {
                        \App::richRelevanceClient()->query('recsForPlacements', [
                            'placements' => 'purchase_complete_page',
                            'productId' => implode('|', array_map(function(Product $p) { return $p->getId(); }, $order->getProduct())),
                            'o'         => $order->getNumberErp(), // Какой формат?
                            'q'         => implode('|', array_map(function(Product $p) { return $p->getQuantity(); }, $order->getProduct())),
                            'pp'         => implode('|', array_map(function(Product $p) { return $p->getPrice(); }, $order->getProduct())),
                        ]);
                    }
                } catch (\Exception $e) {
                    \App::exception()->remove($e);
                }
            }

            // логируем этот шит
            foreach ($orders as $order) {
                $productIds = array_map(function(\Model\Order\Product\Entity $product) { return $product->getId(); }, $order->getProduct());
                $productsForOrder = array_filter($products, function(\Model\Product\Entity $product) use ($productIds) { return in_array($product->getId(), $productIds); });
                $data = [];
                $data['order-number'] = $order->numberErp;
                $data['order-products'] = $productIds;
                $data['order-names'] = array_map(function(\Model\Product\Entity $product) { return $product->getName(); }, $productsForOrder);
                $data['order-product-category'] = array_map(function(\Model\Product\Entity $product) { $category = $product->getRootCategory(); return $category ? $category->getName() : null; }, $productsForOrder);
                $data['order-product-price'] = array_map(function(\Model\Product\Entity $product) { return $product->getPrice(); }, $productsForOrder);
                $data['order-sum'] = $order->getSum();
                $data['order-delivery-price'] = $order->getDelivery() ? $order->getDelivery()->getPrice() : '';
                $data['user-phone'] = $order->getMobilePhone();
                $this->logger($data);
            }

            unset($order, $data, $productIds, $productsForOrder);

        } catch (\Curl\Exception $e) {
            // TODO
        } catch (\Exception $e) {
            // TODO
        }

        // логика первичного просмотра страницы
        $sessionIsReaded = !($this->sessionIsReaded === false);
        $this->session->remove(self::SESSION_IS_READED_KEY);

        $sessionIsReadedAfterAllOnlineOrdersArePaid = $this->sessionIsReadedAfterAllOnlineOrdersArePaid !== false;
        if ($this->areAllOnlineOrdersPaid($orders, $ordersPayment)) {
            $this->session->remove(self::SESSION_IS_READED_AFTER_ALL_ONLINE_ORDERS_ARE_PAID_KEY);
        }

        /** @var string[] $creditDoneOrderIds */
        $creditDoneOrderIds = call_user_func(function() {
            $return = [];

            try {
                $data = \App::session()->get(\App::config()->order['creditStatusSessionKey']);
                $return = array_column(is_array($data) ? $data : [], 'order_id');
            } catch (\Exception $e) {}

            return $return;
        });

        try {
            // SITE-6593 установка order.isCyber
            foreach ($orders as $order) {
                if ($order->prepaidSum) {
                    $order->isCyber = true;
                    break;
                }
            }

            // SITE-6593 сортировка заказов
            uasort($orders, function(\Model\Order\Entity $a, \Model\Order\Entity $b) {
                return (int)$b->isCyber - (int)$a->isCyber;
            });
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $flash = \App::session()->flash();
        $onlineRedirect = false;
        try {
            /** @var \Model\Order\Entity|null $order */
            $order = (1 === count($orders)) ? reset($orders) : null; // только одиночный заказ
            // SITE-6641
            $onlineRedirect =
                isset($flash['onlineRedirect'])
                && (true === $flash['onlineRedirect'])
                && $order && $order->isCyber
            ;
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $page->setParam('orders', $orders);
        $page->setParam('ordersPayment', $ordersPayment);
        $page->setParam('products', $products);
        $page->setParam('productsById', $products);
        $page->setParam('userEntity', $this->user->getEntity());
        $page->setParam('paymentProviders', $paymentProviders);
        $page->setParam('banks', $banks);
        $page->setParam('subscribe', (bool)$request->cookies->get(\App::config()->subscribe['cookieName3']));

        $page->setParam('errors', array_merge( $page->getParam('errors', []), $errors));

        $page->setParam('sessionIsReaded', $sessionIsReaded);
        $page->setParam('sessionIsReadedAfterAllOnlineOrdersArePaid', $sessionIsReadedAfterAllOnlineOrdersArePaid);
        $page->setGlobalParam('creditDoneOrderIds', $creditDoneOrderIds);
        $page->setGlobalParam('onlineRedirect', $onlineRedirect);

        $response = (bool)$orders ? new \Http\Response($page->show()) : new \Http\RedirectResponse($page->url('homepage'));
        $response->headers->setCookie(new \Http\Cookie('enter_order_v3_wanna', 0, 0, '/order',\App::config()->session['cookie_domain'], false, false)); // кнопка "Хочу быстрее"

        return $response;
    }

    /**
     * @param \Model\Order\Entity[] $orders
     * @param \Model\PaymentMethod\PaymentEntity[] $ordersPayment
     * @return bool
     */
    private function areAllOnlineOrdersPaid($orders, $ordersPayment) {
        foreach ($orders as $order) {
            /** @var \Model\PaymentMethod\PaymentEntity|null $paymentEntity */
            $paymentEntity = isset($ordersPayment[$order->getNumber()]) ? $ordersPayment[$order->getNumber()] : null;

            if ($paymentEntity && isset($paymentEntity->methods[$order->getPaymentId()]) && $paymentEntity->methods[$order->getPaymentId()]->isOnline && !$order->isPaid()) {
                return false;
            }
        }

        return true;
    }

    public function getPaymentForm(\Http\Request $request) {
        $methodId = $request->request->get('method');
        $orderId = $request->request->get('order');
        $orderToken = $request->request->get('token');
        $orderNumber = $request->request->get('number');
        $backUrl = $request->request->get('url') ?: \App::router()->generate('orderV3.complete', ['refresh' => 1], true);
        $action = $request->request->get('action');

        $privateClient = \App::coreClientPrivate();

        /** @var \Model\Order\Entity|null $order */
        $order = null;
        if ($orderToken) {
            $order = \RepositoryManager::order()->getEntityByAccessToken($orderToken);
        } else {
            if (!(bool)$this->sessionOrders) {
                throw new \Exception('В сессии нет заказов');
            }
            $sessionOrder = reset($this->sessionOrders);
            $order = \RepositoryManager::order()->getEntityByNumberAndPhone($orderNumber, $sessionOrder['mobile']);
            if (!$order) {
                $order = new \Model\Order\Entity($sessionOrder);
            }
        }

        if (!$order) {
            throw new \Exception('Заказ не получен');
        }

        $data = [
            'method_id' => $methodId,
            'order_id'  => $orderId,
        ];

        if ($action) {
            $data['action_alias'] = $action;
        }

        $result = $privateClient->query('site-integration/payment-config',
            $data,
            [
                'back_ref'    => $backUrl, // обратная ссылка
                'fail_ref'    => $backUrl,
                'email'       => $order->getUser() ? $order->getUser()->getEmail() : '',
                //'card_number' => $order->card,
                'user_token'  => $request->cookies->get('UserTicket'), // токен кросс-авторизации. может быть передан для Связного-Клуба (UserTicket)
            ],
            2 * \App::config()->coreV2['timeout']
        );

        if (!$result) {
            throw new \Exception('Ошибка получения данных payment-config');
        }

        $form = \App::closureTemplating()->render('order/payment/__form', [
            'form'  => $result['detail'],
            'url'   => $result['url'],
            'order' => $order
        ]);

        return new \Http\JsonResponse(['result' => $result, 'form' => $form]);
    }

    /**
     * @deprecated
     * @return \Http\JsonResponse|null
     */
    public function updateCredit() {
        $params = \App::request()->request->all();

        if (!isset($params['number_erp']) || !isset($params['bank_id'])) return null;

        $result = \App::coreClientV2()->query('payment/credit-request',[],[
            'number_erp'    => $params['number_erp'],
            'bank_id'       => $params['bank_id']
        ]);

        return new \Http\JsonResponse($result);
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function setCreditStatus(\Http\Request $request) {
        $sessionKey = \App::config()->order['creditStatusSessionKey'];
        $session = \App::session();

        $form = [
            'order_id' => null,
        ];
        $form = array_merge($form, is_array($request->get('form')) ? $request->get('form') : []);

        if ($form['order_id']) {
            $data = is_array($session->get($sessionKey)) ? $session->get($sessionKey) : [];
            $data[$form['order_id']] = $form;
            $session->set($sessionKey, $data);
        }

        return new \Http\JsonResponse([]);
    }

    /** Оплата баллами Связного
     * @param $request \Http\Request
     * @param $orders array
     * @param $page \View\OrderV3\CompletePage
     * @return bool Флаг об успешности операции
     * @link https://wiki.enter.ru/pages/viewpage.action?pageId=22588834
     */
    private function completeSvyaznoy(\Http\Request $request, $orders, &$page) {

        $error = $request->query->get('Error');

        $data = [
            'OrderId'    => $request->query->get('OrderId'),
            'Status'     => $request->query->get('Status'),
            'Discount'   => $request->query->get('Discount'),
            'CardNumber' => $request->query->get('CardNumber'),
            'Signature'  => $request->query->get('Signature'),
        ];

        try {

            // Если есть ошибка, то в ядро не надо делать запроса
            if ($error) throw new \Exception($error);

            $result = \App::coreClientV2()->query('payment/svyaznoy-club', [], $data, \App::config()->coreV2['hugeTimeout']);

            if (!isset($result['detail']['order']) || !is_array($result['detail']['order'])) {
                throw new \Exception('Не получена информация о заказе');
            }

            // Если всё хорошо, то сохраняем обновленную информацию о заказе
            foreach ($orders as &$order) {
                if ($order['id'] == $result['detail']['order']['id']) {
                    $order['payment_status_id'] = Entity::PAYMENT_STATUS_SVYAZNOY_CLUB;
                    $order['meta_data']['payment.svyaznoy_club'] = [$result['detail']['order']['pay_sum']];
                }
            }

            $this->session->set(\App::config()->order['sessionName'] ? : 'lastOrder', $orders);
            return true;

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            if (!empty($e->getCode())) {
                $page->setParam('errors', array_merge(
                    $page->getParam('errors', []),
                    [[
                        'code' => $e->getCode(),
                        'message' => \App::config()->debug ? $e->getMessage() : 'Ошибка списания баллов Связного Клуба'
                    ]]
                ));
            }
            return false;
        }
    }
}