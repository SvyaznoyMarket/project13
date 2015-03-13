<?php

namespace Controller\Cart;

/**
 * Class WarrantyAction
 * @package Controller\Cart
 * @deprecated
 */
class WarrantyAction {
    /**
     * @param int           $warrantyId
     * @param int           $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($warrantyId, $productId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getCart();

        $warrantyId = (int)$warrantyId;
        $productId = (int)$productId;
        $quantity = (int)$request->get('quantity', 1);

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
                (new ProductAction())->set($product->getId(), $request);
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

            $completeInfo = [
                'success'   =>  true,
                'cart'      => [
                    'sum'           => $cartWarranty ? $cartWarranty->getSum() : 0,
                    'quantity'      => $quantity,
                    'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity() +$cart->getWarrantiesQuantity(),
                    'full_price'    => $cart->getSum(),
                    'old_price'     => $cart->getOriginalSum(),
                    'link'          => \App::router()->generate('order'),
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
                    'cart'    => ['error' => 'Не удалось добавить гарантию в корзину', 'debug' => $e->getMessage()],
                ])
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        }
    }

    /**
     * @param \Http\Request $request
     * @param $warrantyId
     * @param null $productId
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function delete(\Http\Request $request, $warrantyId, $productId = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $request->query->set('quantity', 0);

        return $this->set($warrantyId, $productId, $request);
    }
}