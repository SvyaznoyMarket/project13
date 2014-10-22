<?php

namespace Controller\OrderV3;

use Model\Order\Entity;
use Model\PaymentMethod\PaymentEntity;

class CompleteAction extends OrderV3 {

    private $sessionOrders;
    private $sessionIsReaded;

    public function __construct() {
        parent::__construct();
        $this->sessionOrders = $this->session->get(\App::config()->order['sessionName'] ? : 'lastOrder');
        $this->sessionIsReaded = $this->session->get(self::SESSION_IS_READED_KEY);
    }

    /**
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        $controller = parent::execute($request);
        if ($controller) {
            return $controller;
        }

        \App::logger()->debug('Exec ' . __METHOD__);

        /** @var \Model\Order\Entity[] $orders */
        $orders = [];
        /** @var \Model\PaymentMethod\PaymentEntity[] $ordersPayment */
        $ordersPayment = [];
        $products = [];
        $paymentProviders = [];
        $privateClient = \App::coreClientPrivate();
        $needCreditBanksData = false;
        /** @var $banks \Model\CreditBank\Entity[] */
        $banks = [];

        try {

            if (!(bool)$this->sessionOrders) throw new \Exception('В сессии нет заказов');

            // забираем заказы и доступные методы оплаты
            foreach ($this->sessionOrders as $sessionOrder) {

                if (!is_array($sessionOrder)) continue;

                // сами заказы
                $this->client->addQuery('order/get-by-mobile', ['number' => $sessionOrder['number'], 'mobile' => $sessionOrder['phone']], [], function ($data) use (&$orders, $sessionOrder) {
                    $data = reset($data);
                    $orders[$sessionOrder['number']] = $data ? new Entity($data) : null;
                });

                // методы оплаты для заказа
                $this->client->addQuery(
                    'payment-method/get-for-order',
                    [
                        'geo_id'     => $this->user->getRegionId(),
                        'client_id'  => 'site',
                        'number_erp' => $sessionOrder['number_erp']
                    ],
                    [],
                    function ($data) use ($sessionOrder, &$ordersPayment) {
                        $ordersPayment[$sessionOrder['number']] = new PaymentEntity($data);
                    },
                    function (\Exception $e) {
                        \App::logger()->error(['error' => $e, 'message' => 'Не получены методы оплаты'], ['order']);
                        \App::exception()->remove($e);
                    }
                );
            }

            unset($sessionOrder);

            $this->client->execute();

            sort($orders);

            // получаем продукты для заказов
            foreach ($orders as $order) {

                /** @var $order \Model\Order\Entity */
                \RepositoryManager::product()->prepareCollectionById(array_map(function(\Model\Order\Product\Entity $product) { return $product->getId(); }, $order->getProduct()), null, function ($data) use ($order, &$products) {
                    foreach ($data as $productData) {
                        $products[$productData['id']] = new \Model\Product\Entity($productData);
                    }
                } );

                // Нужны ли нам кредитные банки?
                if ($order->paymentId == \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT) $needCreditBanksData = true;

            }

            // Запрашиваем данные по кредитным банкам
            if ($needCreditBanksData) {
                \RepositoryManager::creditBank()->prepareCollection(function($data) use (&$banks){
                    foreach ($data as $item) {
                        if (isset($item['token'])) $banks[$item['token']] = new \Model\CreditBank\Entity($item);
                    }
                });
            }

            $this->client->execute();
            $privateClient->execute();
            unset($order, $methodId, $onlineMethodsId, $privateClient, $needCreditBanksData);

            // очищаем корзину от заказанных продуктов
            foreach ($products as $product) {
                if ($product instanceof \Model\Product\Entity) $this->cart->setProduct($product, 0);
            }
            unset($product);

            // логируем этот шит
            foreach ($orders as $order) {
                $productIds = array_map(function(\Model\Order\Product\Entity $product) { return $product->getId(); }, $order->getProduct());
                $productsForOrder = array_filter($products, function(\Model\Product\Entity $product) use ($productIds) { return in_array($product->getId(), $productIds); });
                $data = [];
                $data['order-number'] = $order->numberErp;
                $data['order-products'] = $productIds;
                $data['order-names'] = array_map(function(\Model\Product\Entity $product) { return $product->getName(); }, $productsForOrder);
                $data['order-product-category'] = array_map(function(\Model\Product\Entity $product) { $category = $product->getMainCategory(); return $category ? $category->getName() : null; }, $productsForOrder);
                $data['order-product-price'] = array_map(function(\Model\Product\Entity $product) { return $product->getPrice(); }, $productsForOrder);
                $data['order-sum'] = $order->getSum();
                $data['order-delivery-price'] = isset($order->getDelivery()[0]) ? $order->getDelivery()[0]->getPrice() : '';
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

        $page = new \View\OrderV3\CompletePage();
        $page->setParam('orders', $orders);
        $page->setParam('ordersPayment', $ordersPayment);
        $page->setParam('products', $products);
        $page->setParam('userEntity', $this->user->getEntity());
        $page->setParam('paymentProviders', $paymentProviders);
        $page->setParam('banks', $banks);

        $page->setParam('sessionIsReaded', $sessionIsReaded);

        $response = new \Http\Response($page->show());
        $response->headers->setCookie(new \Http\Cookie('enter_order_v3_wanna', 0, 0, '/order',\App::config()->session['cookie_domain'], false, false)); // кнопка "Хочу быстрее"
        return $response;
    }

    public function getPaymentForm(\Http\Request $request, $methodId, $orderId, $orderNumber) {
        $form = '';
        $privateClient = \App::coreClientPrivate();

        if (!(bool)$this->sessionOrders) throw new \Exception('В сессии нет заказов');
        $sessionOrder = reset($this->sessionOrders);

        $order = \RepositoryManager::order()->getEntityByNumberAndPhone($orderNumber, $sessionOrder['phone']);

        $result = $privateClient->query('site-integration/payment-config',
            [
                'method_id' => $methodId,
                'order_id'  => $orderId,
            ],
            [
                'back_ref'    => \App::router()->generate('orderV3.complete', [], true),// обратная ссылка
                'email'       => $order->getUser() ? $order->getUser()->getEmail() : '',
//                            'card_number' => $order->card,
                'user_token'  => $request->cookies->get('UserTicket'),// токен кросс-авторизации. может быть передан для Связного-Клуба (UserTicket)
            ],
            \App::config()->coreV2['hugeTimeout']
        );

        if (!$result) throw new \Exception('Ошибка получения данных payment-config');

        switch ($methodId) {
            case '5':
                $formEntity = (new \Payment\Psb\Form());
                $formEntity->fromArray($result['detail']);
                $form = (new \Templating\HtmlLayout())->render('order/payment/form-psb', array(
                    'provider' => new \Payment\Psb\Provider(['payUrl' => $result['url']]),
                    'order' => $order,
                    'form' => $formEntity
                ));
                break;
            case '8':
                $formEntity = (new \Payment\PsbInvoice\Form());
                $formEntity->fromArray($result['detail']);
                $form = (new \Templating\HtmlLayout())->render('order/payment/form-psbInvoice', array(
                    'provider' => new \Payment\PsbInvoice\Provider(['payUrl' => $result['url']]),
                    'order' => $order,
                    'form' => $formEntity
                ));
                break;
            case '13':
                $form = (new \Templating\HtmlLayout())->render('order/payment/form-paypal', array(
                    'url' => $result['url']
                ));
                break;
        }

        $response = new \Http\JsonResponse(['result' => $result, 'form' => $form]);
        return $response;
    }

    public function updateCredit() {
        $params = \App::request()->request->all();

        if (!isset($params['number_erp']) || !isset($params['bank_id'])) return null;

        $result = \App::coreClientV2()->query('payment/credit-request',[],[
            'number_erp'    => $params['number_erp'],
            'bank_id'       => $params['bank_id']
        ]);

        return new \Http\JsonResponse($result);

    }
}