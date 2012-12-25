<?php

namespace Controller\Order;

class ExternalCreateAction {
    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        $user = \App::user();
        $cart = $user->getCart();

        $productInCart = (array)$request->get('items');
        $regionId = (int)$request->get('city_id');
        $region = $regionId ? \RepositoryManager::getRegion()->getById($regionId) : null;
        if (!$region) {
            $region = \RepositoryManager::getRegion()->getDefaultEntity();
            \App::logger()->warn('Не передан регион для заказа извне');
        }

        if (!(bool)$productInCart) {
            throw new \Exception\NotFoundException('Не переданы товары в корзине для заказа извне');
        }

        /** @var $productsById \Model\Product\Entity[] */
        $productsById = array();
        foreach ($productInCart as $id => $quantity) {
            $productsById[$id] = null;
        }
        foreach (\RepositoryManager::getProduct()->getCollectionById(array_keys($productsById)) as $product) {
            $productsById[$product->getId()] = $product;
        }

        // очистка корзины
        $cart->clear();
        // наполнение корзины товарами из заказа извне
        foreach ($productInCart as $id => $quantity) {
            /** @var $product \Model\Product\Entity|null */
            $product = isset($productsById[$id]) ? $productsById[$id] : null;
            if (!$product) {
                \App::logger()->error(sprintf('Товар #%s из заказа извне не найден', $product->getId()));
                continue;
            }

            $cart->setProduct($product, $quantity);
        }

        $params = array();
        foreach ($request->query->all() as $k => $v) {
            if (0 === strpos($k, 'utm_')) {
                $params[$k] = $v;
            }
        }

        $response = new \Http\RedirectResponse(\App::router()->generate('order.create', $params));
        $user->changeRegion($region, $response);

        return $response;
    }
}