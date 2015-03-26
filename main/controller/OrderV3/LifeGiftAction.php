<?php


namespace Controller\OrderV3;


class LifeGiftAction {

    /** @var \Model\User\Entity|null */
    private $user;

    public function __construct() {
        $this->user = \App::user()->getEntity();
    }

    public function execute(\Http\Request $request, $productId) {
        //\App::logger()->debug('Exec ' . __METHOD__);
        $error = null;
        $product = null;
        $responseData = null;

        try {

            if (!\App::config()->lifeGift['enabled']) throw new \Exception('Акция отключена');

            if ($request->isMethod('POST') && $request->isXmlHttpRequest()) {
                // создаем заказ
                $createdOrder = $this->createOrder($request, $productId);
                // получаем его из БД
                $fullOrder = $this->getOrder($request, $createdOrder);
                // получаем данные формы
                $form = $this->getPaymentForm($fullOrder, $request->get('paynow') == 'card');
                return new \Http\JsonResponse(['form' => $form]);
            }

            $lifegiftRegion = \RepositoryManager::region()->getEntityById(\App::config()->lifeGift['regionId']);

            $product = \RepositoryManager::product()->getEntityById($productId, $lifegiftRegion);

            if ($product === null) throw new \Exception("Товар $productId не найден");
            if ($product->getPrice() == 0) throw new \Exception('Некорректная цена продукта');
            if (!$product->getIsBuyable()) throw new \Exception('Нет остатков по продукту');
            if (!$product->getLabel() || $product->getLabel()->getId() != \App::config()->lifeGift['labelId']) throw new \Exception(sprintf("Товар %s не участвует в акции", $product->getName()));

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            $error = $e->getMessage();
            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        $page = new \View\OrderV3\LifeGiftPage();
        $page->setParam('product', $product);
        $page->setParam('user', $this->user);
        $page->setParam('error', $error);

        return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['result' => $responseData]) : new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @param $productID
     * @return \Model\Order\CreatedEntity|null
     */
    private function createOrder(\Http\Request $request, $productID) {

        $order = null;

        // общие данные заказа
        $orderData = [
            'type_id'           => \Model\Order\Entity::TYPE_ORDER, /* ОБЯЗАТЕЛЬНО */
            'geo_id'            => \App::config()->lifeGift['regionId'], /* ОБЯЗАТЕЛЬНО */
            'shop_id'           => 172,
            'user_id'           => $this->user ? $this->user->getId() : null,
            'is_legal'          => $this->user ? $this->user->getIsCorporative() : false,
            'payment_id'        => $request->get('paynow') == 'card' ? \Model\Order\Entity::PAYMENT_TYPE_ID_CREDIT_CARD_ONLINE : \Model\Order\Entity::PAYMENT_TYPE_ID_PAYPAL,
            'first_name'        => $request->get('user_name'),
            'email'             => $request->get('user_email'),
            'mobile'            => $request->get('user_phone'),
            'extra'             => $request->get('comment'),
            'delivery_type_id'  => \App::config()->lifeGift['deliveryTypeId'],
            'delivery_date'     => null,
            'ip'                => $request->getClientIp(),
            'product'           => [[
                'id'       => $productID,
                'quantity' => 1,
            ]],
        ];

        $params = [];

        if ($this->user && $this->user->getToken()) $params['user_token'] = $this->user->getToken();

        try {
            $result = \App::coreClientV2()->query('order/create-packet2', $params, [$orderData], \App::config()->coreV2['hugeTimeout']);
            if (is_array($result) && isset($result[0])) $order = new \Model\Order\CreatedEntity($result[0]);
            if (!$order->getId() || !$order->getNumber()) {
                throw new \Exception('Ошибка создания заказа');
            }
        } catch(\Exception $e) {
            $result = $e->getMessage();
        }

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order']);

        return $order;
    }

    /**
     * @param \Http\Request $request
     * @param \Model\Order\CreatedEntity $order
     * @return \Model\Order\Entity|null
     * @throws \Exception
     */
    private function getOrder(\Http\Request $request, \Model\Order\CreatedEntity $order) {
        $o = \RepositoryManager::order()->getEntityByNumberAndPhone($order->getNumber(), $request->get('user_phone'));
        if ($o == null) throw new \Exception('Неудалось получить заказ');
        return $o;
    }

    private function getPaymentForm(\Model\Order\Entity $order, $isCardPayment) {

        $methodId = $isCardPayment ? 5 : 13;

        $privateClient = $privateClient = \App::coreClientPrivate();

        $coreResponse = $privateClient->query('site-integration/payment-config',
            [
                'method_id' => $methodId,
                'order_id'  => $order->getId(),
            ],
            [
                'back_ref'    => \App::router()->generate('orderV3.lifegift.complete', [], true),// обратная ссылка
            ],
            \App::config()->coreV2['hugeTimeout']
        );

        if (!$coreResponse) throw new \Exception('Ошибка получения данных payment-config');

        if ($methodId == 5) {
            $formEntity = (new \Payment\Psb\Form());
            $formEntity->fromArray($coreResponse['detail']);
            $form = (new \Templating\HtmlLayout())->render('order/payment/form-psb', array(
                'provider' => new \Payment\Psb\Provider(['payUrl' => $coreResponse['url']]),
                'order' => $order,
                'form' => $formEntity
            ));
        } else {
            $form = (new \Templating\HtmlLayout())->render('order/payment/form-paypal', array(
                'url'           => $coreResponse['url'],
                'url_params'    => isset($coreResponse['url_params']) && !empty($coreResponse['url_params']) ? $coreResponse['url_params'] : null
            ));
        }

        return $form;

    }

    public function complete() {
        $message = 'Спасибо!';
        $page = new \View\OrderV3\LifeGiftPage();
        $page->setParam('message', $message);
        return new \Http\Response($page->show());
    }


}
