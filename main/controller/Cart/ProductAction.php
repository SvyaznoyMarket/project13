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
            $this->updateCartWarranty($product, $cartProduct, $quantity);

            $returnRedirect = $request->headers->get('referer') ?: \App::router()->generate('homepage');
            switch (\App::abTest()->getCase()->getKey()) {
                case 'upsell':
                    $returnRedirect = \App::router()->generate('product.upsell', ['productToken' => $product->getToken()]);
                    break;
                case 'order2cart':
                    $returnRedirect = \App::router()->generate('cart');
                    break;
            }

            // crossss
            if (\App::config()->crossss['enabled'] && ($quantity > 0)) {
                (new \Controller\Crossss\CartAction())->product($product);
            }

            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => true,
                    'data'    => [
                        'sum'           => $cartProduct ? $cartProduct->getSum() : 0,
                        'quantity'      => $quantity,
                        'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity() + $cart->getWarrantiesQuantity(),
                        'full_price'    => $cart->getSum(),
                        'old_price'     => $cart->getOriginalSum(),
                        'link'          => \App::router()->generate('order.create'),
                    ],
                    'result'  => \Kissmetrics\Manager::getCartEvent($product),
                ])
                : new \Http\RedirectResponse($returnRedirect);
        } catch (\Exception $e) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => false,
                    'data'    => ['error' => 'Не удалось товар услугу в корзину', 'debug' => $e->getMessage()],
                ])
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        }
    }


    public function setList(\Http\Request $request) {
        $region = \App::user()->getRegion();
        $cart = \App::user()->getCart();

        $responseData = [];

        try {
            $productData = (array)$request->get('product');
            if (!(bool)$productData) {
                throw new \Exception('Не получены данные о товарах');
            }

            /** @var $productsById \Model\Product\CompactEntity[] */
            $productsById = [];
            /** @var $productQuantitiesById array */
            $productQuantitiesById = [];
            foreach ($productData as $productItem) {
                $productId = (int)$productItem['id'];
                if (!$productId) {
                    \App::logger()->error('Не указан ид товара');
                    continue;
                }
                $productQuantity = (int)$productItem['quantity'];
                if (!$productQuantity) {
                    \App::logger()->error('Не указано количество товара');
                    continue;
                }

                $productsById[$productId] = null;
                $productQuantitiesById[$productId] = $productQuantity;
            }

            if (!(bool)$productsById) {
                throw new \Exception('Не собраны ид товаров');
            }

            foreach (array_chunk(array_keys($productsById), 50, true) as $productsInChunk) {
                \RepositoryManager::product()->prepareCollectionById($productsInChunk, $region, function($data) use (&$productsById) {
                    foreach ($data as $item) {
                        $productsById[$item['id']] = new \Model\Product\Entity($item);
                    }
                });
            }
            \App::coreClientV2()->execute();

            $quantity = 0;
            $sum = 0;
            foreach ($productsById as $productId => $product) {
                if (!$product) {
                    \App::logger()->error(sprintf('Не получен товар #%s', $productId), ['cart']);
                    continue;
                }
                $productQuantity = isset($productQuantitiesById[$productId]) ? $productQuantitiesById[$productId] : null;
                if (!$productQuantity) continue;

                $cart->setProduct($product, $productQuantity);
                $cartProduct = $cart->getProductById($product->getId());
                $this->updateCartWarranty($product, $cartProduct, $productQuantity);

                $quantity += $productQuantity;
                $sum += $cartProduct->getSum();
            }

            $responseData = [
                'success' => true,
                'data'    => [
                    'sum'           => $sum,
                    'quantity'      => $quantity,
                    'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity() + $cart->getWarrantiesQuantity(),
                    'full_price'    => $cart->getSum(),
                    'old_price'     => $cart->getOriginalSum(),
                    'link'          => \App::router()->generate('order.create'),
                ],
            ];
        } catch(\Exception $e) {
            $responseData = [
                'success' => false,
                'data'    => ['error' => 'Не удалось товар услугу в корзину', 'debug' => $e->getMessage()],
            ];
        }

        return new \Http\JsonResponse($responseData);
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

    protected function updateCartWarranty(\Model\Product\Entity $product, \Model\Cart\Product\Entity $cartProduct = null, $quantity) {
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

                \App::user()->getCart()->setWarranty($warranty, $quantity, $product->getId());
            } catch (\Exception $e) {
                \App::logger()->error($e);
            }
        }
    }
}