<?php

namespace Controller\Cart;

class ProductAction {
    /**
     * @param int           $productId
     * @param int           $quantity
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($productId, $quantity = 1, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getCart();

        $productId = (int)$productId;
        $quantity = (int)$quantity;

        try {
            if ($quantity < 0) {
                $quantity = 0;
                \App::logger()->warn(sprintf('Указано неверное количество товаров. Запрос %s', json_encode($request->request->all())));
            }

            if (!$productId) {
                throw new \Exception('Не получен ид товара');
            }

            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            // не учитываем является ли товар набором или нет - за это отвечает ядро
            $cart->setProduct($product, $quantity);

            // обновить количество гарантий для товара
            foreach ($cart->getWarrantyByProduct($product->getId()) as $cartWarranty) {
                // TODO: доделать гарантии
                //$cart->setWarranty($cartWarranty, $quantity);
            }

            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse(array(
                    'success' => true,
                    'data'    => array(
                        'quantity'      => $quantity,
                        'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity() + $cart->getWarrantiesQuantity(),
                        'full_price'    => $cart->getTotalPrice(),
                        'link'          => \App::router()->generate('order.create'),
                    ),
                ))
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        } catch (\Exception $e) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse(array(
                    'success' => false,
                    'data'    => array('error' => 'Не удалось товар услугу в корзину', 'debug' => $e->getMessage()),
                ))
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        }
    }

    /**
     * @param $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function delete(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        return $this->set($productId, 0, $request);
    }
}