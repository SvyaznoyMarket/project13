<?php


namespace Controller\OrderV3;


class LifeGiftAction {

    /** @var \Model\User\Entity|null */
    private $user;

    public function __construct() {
        $this->user = \App::user()->getEntity();
    }

    public function execute(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);
        $error = null;
        $product = null;

        try {

            if (!\App::config()->lifeGift['enabled']) throw new \Exception('Акция отключена');

            if ($request->isMethod('POST')) {
                $this->createOrder($request, $productId);
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
        }

        $page = new \View\OrderV3\LifeGiftPage();
        $page->setParam('product', $product);
        $page->setParam('user', $this->user);
        $page->setParam('error', $error);

        return new \Http\Response($page->show());
    }

    private function createOrder(\Http\Request $request, $productID) {

        // общие данные заказа
        $orderData = [
            'type_id'           => \Model\Order\Entity::TYPE_ORDER, /* ОБЯЗАТЕЛЬНО */
            'geo_id'            => \App::config()->lifeGift['regionId'], /* ОБЯЗАТЕЛЬНО */
            'user_id'           => $this->user ? $this->user->getId() : null,
            'is_legal'          => $this->user ? $this->user->getIsCorporative() : false,
            'payment_id'        => \Model\Order\Entity::PAYMENT_TYPE_ID_CREDIT_CARD_ONLINE,
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
        if ($this->user && $this->user->getToken()) {
            $params['user_token'] = $this->user->getToken();
        }

        try {
            $result = \App::coreClientV2()->query('order/create-packet2', $params, [$orderData], \App::config()->coreV2['hugeTimeout']);
        } catch(\Exception $e) {

        }

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order']);

        $redirectUrl = '';
        return new \Http\RedirectResponse($redirectUrl);
    }


    private function getPaymentForm(){

    }


}