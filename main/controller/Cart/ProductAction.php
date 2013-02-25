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
                \App::logger()->warn(sprintf('Указано неверное количество товаров. Запрос %s', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE)));
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

            $cartProduct = $cart->getProductById($product->getId());

            // обновить количество гарантий для товара
            if ($cartProduct && (bool)$cartProduct->getWarranty()) {

                try {
                    $cartWarranties = $cartProduct->getWarranty();
                    /** @var $cartWarranty \Model\Cart\Warranty\Entity|null */
                    $cartWarranty = reset($cartWarranties);
                    if (!$cartWarranty) {
                        throw new \Exception(sprintf('Не найдена расширенная гарантия на товар #%s', $product->getId()));
                    }

                    $warranty = null;
                    foreach ($product->getWarranty() as $iWarranty) {
                        if ($iWarranty->getId() == $cartWarranty->getId()) {
                            $warranty = $iWarranty;
                            break;
                        }
                    }
                    if (!$warranty) {
                        throw new \Exception(sprintf('Не найдена расширенная гарантия #%s на товар #%s', $cartWarranty->getId(), $product->getId()));
                    }

                    $cart->setWarranty($warranty, $quantity, $product->getId());
                } catch (\Exception $e) {
                    \App::logger()->error($e);
                }
            }

            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => true,
                    'data'    => [
                        'quantity'      => $quantity,
                        'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity() + $cart->getWarrantiesQuantity(),
                        'full_price'    => $cart->getSum(),
                        'link'          => \App::router()->generate('order.create'),
                    ],
                ])
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        } catch (\Exception $e) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => false,
                    'data'    => ['error' => 'Не удалось товар услугу в корзину', 'debug' => $e->getMessage()],
                ])
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