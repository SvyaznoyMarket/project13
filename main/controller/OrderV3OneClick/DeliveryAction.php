<?php

namespace Controller\OrderV3OneClick;

use Curl\TimeoutException;

class DeliveryAction {

    public function __construct() {
        $this->session = \App::session();
        $this->splitSessionKey = \App::config()->order['oneClickSplitSessionKey'];
        $this->client = \App::coreClientV2();
        $this->user = \App::user();
    }

    /** Main function
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $product_list = (array)$request->get('products');
        $shopId = is_scalar($request->get('shopId')) ? $request->get('shopId') : null;
        if (!$shopId) {
            $params = $request->get('params');
            if (!empty($params['shopId'])) {
                $shopId = $params['shopId'];
            }
        }

        foreach ($product_list as &$productItem) {
            $productItem['quantity'] = 1;
        }
        if (isset($productItem)) unset($productItem);

        if ($request->isXmlHttpRequest()) {

            $splitData = [];
            try {

                $previousSplit = $this->session->get($this->splitSessionKey);

                if (!$previousSplit || !$request->get('update')) {
                    $result['OrderDeliveryRequest'] = json_encode($splitData, JSON_UNESCAPED_UNICODE);
                    $result['OrderDeliveryModel'] = $this->getSplit(null, $shopId, $product_list);
                } else {
                    $splitData = [
                        'previous_split' => $previousSplit,
                        'changes'        => $this->formatChanges($request->request->all(), $previousSplit)
                    ];

                    $result['OrderDeliveryRequest'] = json_encode($splitData, JSON_UNESCAPED_UNICODE);
                    $result['OrderDeliveryModel'] = $this->getSplit($request->request->all(), $shopId, $product_list);
                }

                $result['page'] = \App::closureTemplating()->render('order-v3-1click/__delivery', [
                    'orderDelivery' => $result['OrderDeliveryModel'],
                    'shopId'        => $shopId,
                ]);

                if (is_array($result['OrderDeliveryModel']->orders)) {
                    /** @var \Model\OrderDelivery\Entity\Order $order */
                    $order = reset($result['OrderDeliveryModel']->orders);
                    if ($order->prepaid_sum) {
                        $result['prepaymentMessage'] = 'Требуется предоплата';
                    }
                }

                $quantityError = array_filter($result['OrderDeliveryModel']->errors, function(\Model\OrderDelivery\Error $error){ return $error->code == 708; });

                if ($quantityError && isset($quantityError[0]->details['max_available_quantity'])) {
                    $result['warn'] = 'Запрошенного количества нет в наличии, будет оформлено '. $quantityError[0]->details['max_available_quantity'] . ' шт.';
                }

            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e->getMessage(), ['curl', 'cart/split']);
                $result['error']    = ['message' => $e->getMessage()];
                $result['errorContent'] = \App::closureTemplating()->render('order-v3/__error', ['error' => $e->getMessage()]);
                $result['data']     = ['data' => $splitData];
                if ($e->getCode() == 600) {
                    //$result['redirect'] = \App::router()->generateUrl('cart');
                }
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e->getMessage(), ['cart/split']);
                $result['error'] = ['message' => $e->getMessage()];
                $result['errorContent'] = \App::closureTemplating()->render('order-v3/__error', ['error' => $e->getMessage()]);
            }

            return new \Http\JsonResponse(['result' => $result], isset($result['error']) ? 500 : 200);
        }

        try {

            // сохраняем данные пользователя
            $data['action'] = 'changeUserInfo';
            $data['user_info'] = $this->session->get($this->splitSessionKey)['user_info'];

            //$orderDelivery =  new \Model\OrderDelivery\Entity($this->session->get($this->splitSessionKey));
            $orderDelivery = $this->getSplit($data);

            $page = new \View\OrderV3\DeliveryPage();
            $page->setParam('orderDelivery', $orderDelivery);
            return new \Http\Response($page->show());

        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e->getMessage(), ['curl', 'cart/split']);
            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', 'CORE: '.$e->getMessage());
            $page->setParam('step', 2);
            return new \Http\Response($page->show(), 500);
        } catch (\Exception $e) {
            \App::logger()->error($e->getMessage(), ['cart/split']);
            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', $e->getMessage());
            $page->setParam('step', 2);
            return new \Http\Response($page->show(), 500);
        }

    }

    /**
     * @param array $data
     * @param null $shopId
     * @param $product_list
     * @return \Model\OrderDelivery\Entity
     * @throws \Exception
     */
    public function getSplit(array $data = null, $shopId = null, $product_list = []) {
        if ($data) {
            $splitData = [
                'previous_split' => $this->session->get($this->splitSessionKey),
                'changes'        => $this->formatChanges($data, $this->session->get($this->splitSessionKey))
            ];
        } else {
            if (!$product_list) throw new \Exception('Пустая корзина');

            $splitData = [
                'cart' => [
                    'product_list' => $product_list
                ]
            ];
        }

        if ($shopId) {
            $splitData['shop_id'] = (int)$shopId;
        }

        $orderDeliveryData = null;
        foreach ([2, 3] as $i) { // две попытки на расчет доставки: 2*4 и 3*4 секунды
            try {
                $orderDeliveryData = $this->client->query(
                    'cart/split',
                    [
                        'geo_id'     => $this->user->getRegion()->getId(),
                        'request_id' => \App::$id, // SITE-4445
                    ],
                    $splitData,
                    $i * \App::config()->coreV2['timeout']
                );
            } catch (TimeoutException $e) {
                \App::exception()->remove($e);
            }

            if ($orderDeliveryData) break; // если получен ответ прекращаем попытки
        }
        if (!$orderDeliveryData) {
            throw new \Exception('Не удалось расчитать доставку. Повторите попытку позже.');
        }

        $orderDelivery = new \Model\OrderDelivery\Entity($orderDeliveryData);
        if (!(bool)$orderDelivery->orders) {
            foreach ($orderDelivery->errors as $error) {
                if (708 == $error->code) {
                    throw new \Exception('Товара нет в наличии');
                }
            }

            throw new \Exception('Отстуствуют данные по заказам');
        }

        \RepositoryManager::order()->prepareOrderDeliveryProducts($orderDelivery);

        \App::coreClientV2()->execute();

        // сохраняем в сессию расчет доставки
        $this->session->set($this->splitSessionKey, $orderDeliveryData);

        return $orderDelivery;
    }

    private function formatChanges($data, $previousSplit) {

        $changes = [];

        switch ($data['action']) {

            case 'changeUserInfo':
                $changes['user_info'] = array_merge($previousSplit['user_info'], $data['user_info']);
                break;

            case 'changeDelivery':
                $changes['orders'] = array(
                    $data['params']['block_name'] => array_merge(
                        (array)$previousSplit['orders'][$data['params']['block_name']],
                        array( 'delivery' => array( 'delivery_method_token' => $data['params']['delivery_method_token'] ) )
                    )
                );
                break;

            case 'changePoint':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                // SITE-5703 TODO remove
                $true_token = strpos($data['params']['token'], '_postamat') !== false ? str_replace('_postamat', '', $data['params']['token']) : $data['params']['token'];
                $changes['orders'][$data['params']['block_name']]['delivery']['point'] = ['id' => $data['params']['id'], 'token' => $true_token];
                break;

            case 'changeDate':
                //$this->logger(['action' => 'change-date']);
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['delivery']['date'] = $data['params']['date'];
                break;

            case 'changeInterval':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['delivery']['interval'] = $data['params']['interval'];
                break;

            case 'changePaymentMethod':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                if (isset($data['params']['by_credit_card']) && $data['params']['by_credit_card'] == 'true') $paymentTypeId = \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY;
                else if (isset($data['params']['by_online_credit']) && $data['params']['by_online_credit'] == 'true') $paymentTypeId = \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT;
                else $paymentTypeId = \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CASH;
                $changes['orders'][$data['params']['block_name']]['payment_method_id'] = $paymentTypeId;
                break;

            case 'changeProductQuantity':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );

                $id = $data['params']['id'];
                $quantity = $data['params']['quantity'];
                $productsArray = &$changes['orders'][$data['params']['block_name']]['products'];

                array_walk($productsArray, function(&$product) use ($id, $quantity) {
                        if ($product['id'] == $id) $product['quantity'] = (int)$quantity;
                });
                break;
            case 'changeAddress':
                $changes['user_info'] = $previousSplit['user_info'];
                $changes['user_info']['address'] = array_merge($changes['user_info']['address'], $data['params']);
                break;
            case 'changeOrderComment':
                $changes['orders'] = $previousSplit['orders'];
                foreach ($changes['orders'] as &$order) {
                    $order['comment'] = $data['params']['comment'];
                }
                break;
            case 'applyDiscount':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['discounts'][] = ['number' => $data['params']['number'], 'name' => null, 'type' => null, 'discount' => null];
                break;
            case 'deleteDiscount':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['discounts'] = array_filter($changes['orders'][$data['params']['block_name']]['discounts'], function($discount) use ($data) {
                    return $discount['number'] != $data['params']['number'];
                });
                break;
            case 'applyCertificate':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                $changes['orders'][$data['params']['block_name']]['certificate'] = ['code' => $data['params']['code'], 'pin' => $data['params']['pin']];
                break;
            case 'deleteCertificate':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                $changes['orders'][$data['params']['block_name']]['certificate'] = null;
                break;
        }

        return $changes;

    }


}