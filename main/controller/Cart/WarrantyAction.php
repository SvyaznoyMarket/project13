<?php

namespace Controller\Cart;

class WarrantyAction {
    /**
     * @param int           $warrantyId
     * @param int           $productId
     * @param int           $quantity
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($warrantyId, $productId, $quantity = 1, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getCart();

        $warrantyId = (int)$warrantyId;
        $productId = (int)$productId;
        $quantity = (int)$quantity;

        try {
            if ($quantity < 0) {
                $quantity = 0;
                \App::logger()->warn(sprintf('Указано неверное количество гарантий. Запрос %s', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE)));
            }

            if (!$warrantyId) {
                throw new \Exception('Не получен ид гарантии');
            }

            $product = null;
            if ($productId) {
                $product = \RepositoryManager::product()->getEntityById($productId);
                if (!$product) {
                    throw new \Exception(sprintf('Товар #%s для гарантии #%s не найден', $productId, $warrantyId));
                }
            }

            // если в корзине нет товара
            if ($product && !$cart->hasProduct($product->getId())) {
                $action = new ProductAction();
                $action->set($product->getId(), $quantity, $request);
            }

            // TODO: на ядре пока нет метода для получения гарантии по ид
            $warranty = null;
            foreach ($product->getWarranty() as $iWarranty) {
                if ($iWarranty->getId() == $warrantyId) {
                    $warranty = $iWarranty;
                    break;
                }
            }
            if (!$warranty) {
                throw new \Exception(sprintf('Товар #%s не найден', $warrantyId));
            }

            $cart->setWarranty($warranty, $quantity, $productId);

            $cartWarranty = null;
            if ($product && $cartProduct = $cart->getProductById($product->getId())) {
                $cart->fill(); // костыль
                $cartWarranty = $cartProduct->getWarrantyById($warranty->getId());
            } else {
                $cartWarranty = $cart->getWarrantyById($warranty->getId());
            }

            $productInfo = [];
            $warrantyInfo = [];
            if ($product) {
                $productInfo = [
                    'name'  =>  $product->getName(),
                    'img'   =>  $product->getImageUrl(2),
                    'link'  =>  $product->getLink(),
                    'price' =>  $product->getPrice(),
                ];
            }
            if (\App::config()->kissmentrics['enabled']) {
                $kissInfo = \Kissmetrics\Manager::getCartEvent($product, $cartWarranty);
                if (isset($kissInfo['product'])) $productInfo = array_merge($productInfo, $kissInfo['product']);
                if (isset($kissInfo['warranty'])) $warrantyInfo = $kissInfo['warranty'];
            }

            $completeInfo = [
                'success'   =>  true,
                'cart'      => [
                    'sum'           => $cartWarranty ? $cartWarranty->getSum() : 0,
                    'quantity'      => $quantity,
                    'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity() +$cart->getWarrantiesQuantity(),
                    'full_price'    => $cart->getSum(),
                    'old_price'     => $cart->getOriginalSum(),
                    'link'          => \App::router()->generate('order.create'),
                ],
            ];
            if ($productInfo) $completeInfo['product'] = $productInfo;
            if ($warrantyInfo) $completeInfo['service'] = $warrantyInfo;

            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse($completeInfo)
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        } catch (\Exception $e) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => false,
                    'data'    => ['error' => 'Не удалось добавить гарантию в корзину', 'debug' => $e->getMessage()],
                ])
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        }
    }

    /**
     * @param $warrantyId
     * @param $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function delete(\Http\Request $request, $warrantyId, $productId = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        return $this->set($warrantyId, $productId, 0, $request);
    }
}