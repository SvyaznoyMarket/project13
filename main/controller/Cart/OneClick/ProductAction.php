<?php

namespace Controller\Cart\OneClick;

class ProductAction {
    /**
     * @param int           $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($productId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getOneClickCart();

        $productId = (int)$productId;
        $quantity = (int)$request->get('quantity', 1);

        /** @var $product \Model\Product\Entity|null */
        $product = null;

        $responseData = [];
        try {
            if ($quantity < 0) {
                $quantity = 0;
                \App::logger()->warn(['message' => 'Указано неверное количество товаров', 'request' => $request->request->all()]);
            }

            if (!$productId) {
                throw new \Exception('Не получен ид товара');
            }

            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $cartProduct = new \Model\Cart\Product\Entity([
                'id'       => $product->getId(),
                'price'    => $product->getPrice(),
                'quantity' => $quantity,
                'sum'      => $product->getPrice() * $quantity,
            ]);

            $cart->setProduct($cartProduct);
            if ($request->get('shopId')) $cart->setShop($request->get('shopId'));

            $parentCategoryId = $product->getParentCategory() ? $product->getParentCategory()->getId() : null;

            $params = [];
            if ($request->get('shopId')) $params['shopId'] = $request->get('shopId');

            $responseData['success']  = true;
            $responseData['redirect'] = \App::router()->generate('order.oneClick.new', $params);
            $responseData['cart']     = [
                'sum'           => $cartProduct ? $cartProduct->getSum() : 0,
                'quantity'      => $quantity,
                'full_quantity' => $cartProduct->getQuantity(),
                'full_price'    => $cart->getSum(),
                'old_price'     => $cart->getSum(),
                'link'          => $product->getLink(),
                'category_id'   => $parentCategoryId,
            ];
            $responseData['regionId'] = \App::user()->getRegionId();
            $responseData['product'] = [
                'id'        => $product->getId(),
                'quantity'  => $quantity,
            ];
        } catch (\Exception $e) {
            \App::logger()->error($e, ['order', 'one-click']);
            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return $request->isXmlHttpRequest()
            ? new \Http\JsonResponse($responseData)
            : new \Http\RedirectResponse(\App::router()->generate('order.oneClick.new'));
    }

    /**
     * @param $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function delete(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $request->query->set('quantity', 0);

        return $this->set($productId, $request);
    }
}