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
        $sender = $request->query->get('sender');
        $sender2 = (string)$request->query->get('sender2');
        $params = [];

        if (is_string($sender) && !empty($sender)) {
            $sender = ['name' => $sender];
        }

        if (!is_array($sender)) {
            $sender = null;
        }

        if ($sender) {
            $params['sender'] = $sender;
        }

        if ($sender2) {
            $params['sender2'] = $sender2;
        }

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

            $cart->clear();
            $cart->setProduct($product, $quantity, $params);
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

    public function setList(\Http\Request $request) {
        $region = \App::user()->getRegion();
        $cart = \App::user()->getOneClickCart();
        $sender = $request->query->get('sender');
        $sender2 = (string)$request->query->get('sender2');
        $params = [];

        if (is_string($sender) && !empty($sender)) {
            $sender = ['name' => $sender];
        }

        if (!is_array($sender)) {
            $sender = null;
        }

        if ($sender) {
            $params['sender'] = $sender;
        }

        if ($sender2) {
            $params['sender2'] = $sender2;
        }

        try {
            $productData = (array)$request->get('product');
            if (!$productData) {
                throw new \Exception('Не получены данные о товарах');
            }

            $productQuantitiesById = [];
            foreach ($productData as $productItem) {
                if (isset($productItem['id'])) {
                    $productId = (int)$productItem['id'];
                } else {
                    \App::logger()->error('Не указан ид товара');
                    continue;
                }

                if (isset($productItem['quantity'])) {
                    $productQuantity = (int)$productItem['quantity'];
                } else {
                    $productQuantity = 0;
                }

                if (!$productQuantity) {
                    \App::logger()->error('Не указано количество товара');
                    continue;
                }

                $productQuantitiesById[$productId] = $productQuantity;
            }

            if (!$productQuantitiesById) {
                throw new \Exception('Не собраны ид товаров');
            }

            $cart->clear();

            foreach (array_chunk(array_keys($productQuantitiesById), \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
                \RepositoryManager::product()->prepareCollectionById($productsInChunk, $region, function($data) use (&$productQuantitiesById, $cart, $params) {
                    foreach ($data as $item) {
                        $product = new \Model\Cart\Product\Entity($item);
                        $product->setQuantity($productQuantitiesById[$product->getId()]);
                        $cart->addProduct($product, 1, $params);
                    }
                });
            }

            \App::coreClientV2()->execute();

            $responseData = [
                'success' => true
            ];
        } catch(\Exception $e) {
            $responseData = [
                'success' => false,
                'data'    => ['error' => 'Не удалось добавить товар или услугу в корзину', 'debug' => $e->getMessage()],
            ];
        }

        return $request->isXmlHttpRequest()
            ? new \Http\JsonResponse($responseData)
            : new \Http\RedirectResponse(\App::router()->generate('order.oneClick.new'));
    }

    /**
     * @param \Http\Request $request
     * @param $productId
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function delete(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getOneClickCart();
        $productId = (int)$productId;

        try {
            if (!$productId) {
                throw new \Exception('Не получен ид товара');
            }

            $cart->deleteProduct($productId);

            $responseData = [
                'success' => true
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
     * @param \Http\Request $request
     * @param $productId
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function change(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getOneClickCart();
        $productId = (int)$productId;

        try {
            if (!$productId) {
                throw new \Exception('Не получен id товара');
            }

            $cart->setProductById($productId, $request->get('quantity'));

            $responseData = [
                'success' => true
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
}