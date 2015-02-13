<?php

namespace Controller\Cart;

class ProductAction {

    /**
     * @param int           $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($productId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getCart();

        $productId = (int)$productId;
        $quantity = $request->get('quantity');
        $sender = $request->query->get('sender');
        $params = [];
        $moveProductToUp = false;

        if (is_string($sender) && !empty($sender)) {
            $sender = ['name' => $sender];
        }

        if (!is_array($sender)) {
            $sender = null;
        }

        if ($sender) {
            $params['sender'] = $sender;
        }

        try {
            if (!$productId) {
                throw new \Exception('Не получен ид товара');
            }

            if ($quantity === null) {
                // SITE-5022
                $quantity = $cart->getQuantityByProduct($productId) + 1;
                $moveProductToUp = true;
            } else {
                $quantity = (int)$quantity;
            }

            if ($quantity < 0) {
                $quantity = 0;
                \App::logger()->warn(['message' => 'Указано неверное количество товаров', 'request' => $request->request->all()]);
            }

            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            if (\App::config()->cart['checkStock'] && !empty($product->getStock())) {
                if ($quantity > $product->getStockWithMaxQuantity()->getQuantity()) {
                    throw new \Exception('Нет запрошенного количества товара');
                }
            }

            if ($request->query->get('credit') == 'on') $params['credit'] = ['enabled' => true];

            // не учитываем является ли товар набором или нет - за это отвечает ядро
            $cart->setProduct($product, $quantity, $params, $moveProductToUp);
            $cartProduct = $cart->getProductById($product->getId());

            $returnRedirect = $request->headers->get('referer') ?: ($product->getLink() ?: \App::router()->generate('homepage'));
            if (\App::abTest()->getTest('other')) {
                switch (\App::abTest()->getTest('other') && \App::abTest()->getTest('other')->getChosenCase()->getKey()) {
                    case 'upsell':
                        $returnRedirect = \App::router()->generate('product.upsell', ['productToken' => $product->getToken()]);
                        break;
                    case 'order2cart':
                        $returnRedirect = \App::router()->generate('cart');
                        break;
                }
            }

            $productInfo = [
                'id'        => $product->getId(),
                'article'   => $product->getArticle(),
                'name'      => $product->getName(),
                'img'       => $product->getImageUrl(),
                'link'      => $product->getLink(),
                'price'     => $product->getPrice(),
                'deleteUrl' => $cartProduct  ? (new \Helper\TemplateHelper())->url('cart.product.delete', ['productId' => $cartProduct->getId()]) : null,
                'addUrl'    => !$cartProduct ? (new \Helper\TemplateHelper())->url('cart.product.set',    ['productId' => $product->getId()]) : null,
                'cartButton'     => [
                    'id' => \View\Id::cartButtonForProduct($product->getId()),
                ],
                'isTchiboProduct' => $product->getMainCategory() && 'Tchibo' === $product->getMainCategory()->getName(),
                'category'        => $this->getCategories($product),
                'quantity'        => $cartProduct ? $cartProduct->getQuantity() : 0,
                'serviceQuantity' => $cart->getServicesQuantityByProduct($product->getId()),
            ];
            if (\App::config()->kissmentrics['enabled']) {
                try {
                    $kissInfo = \Kissmetrics\Manager::getCartEvent($product);
                    $productInfo = array_merge($productInfo, $kissInfo['product']);
                } catch (\Exception $e) {
                    \App::logger()->error($e, ['kissmetrics']);
                }
            }

            $parentCategoryId = $product->getParentCategory() ? $product->getParentCategory()->getId() : null;

            if ($request->isXmlHttpRequest()) {
                $response = new \Http\JsonResponse([
                    'success'    => true,
                    'cart'       => [
                        'sum'           => $cartProduct ? $cartProduct->getSum() : 0,
                        'quantity'      => $quantity,
                        'full_quantity' => $cart->getProductsQuantity(),
                        'full_price'    => $cart->getSum(),
                        'old_price'     => $cart->getOriginalSum(),
                        'link'          => \App::router()->generate('order'),
                        'products'      => $cart->getProductsDumpNC(),
                    ],
                    'product'     => $productInfo,
                    'category_id' => $parentCategoryId,
                    'regionId'    => \App::user()->getRegionId(),
                    'sender'      => $sender,
                ]);
            } else {
                $response = new \Http\RedirectResponse($returnRedirect);
            }

            if ($cart->getSum()) {
                \Session\User::enableInfoCookie($response);
            } else {
//                \Session\User::disableInfoCookie($response); // SITE-3926
            }

            return $response;

        } catch (\Exception $e) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => false,
                    'cart'    => ['error' => 'Не удалось добавить товар или услугу в корзину', 'debug' => $e->getMessage()],
                ])
                : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
        }
    }

    private function getCategories(\Model\Product\Entity $product) {
        $categories = [];
        foreach ($product->getCategory() as $category) {
            $categories[] = [
                'id'   => $category->getId(),
                'name' => $category->getName(),
            ];
        }

        return $categories;
    }


    public function setList(\Http\Request $request) {
        $region = \App::user()->getRegion();
        $cart = \App::user()->getCart();
        $client = \App::coreClientV2();
        $sender = $request->query->get('sender');
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

            foreach (array_chunk(array_keys($productsById), \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
                \RepositoryManager::product()->prepareCollectionById($productsInChunk, $region, function($data) use (&$productsById) {
                    foreach ($data as $item) {
                        $productsById[$item['id']] = new \Model\Product\Entity($item);
                    }
                });
            }
            \App::coreClientV2()->execute();

            $quantity = 0;
            foreach ($productsById as $productId => $product) {
                if (!$product) {
                    \App::logger()->error(sprintf('Не получен товар #%s', $productId), ['cart']);
                    continue;
                }
                $productQuantity = isset($productQuantitiesById[$productId]) ? $productQuantitiesById[$productId] : null;
                if (!$productQuantity) continue;


                $cart->setProduct($product, $productQuantity + $cart->getQuantityByProduct($productId), $params, true);

                $quantity += $cart->getQuantityByProduct($productId);
            }
            $cart->fill();

            $result = [];
            $client->addQuery(
                'cart/get-price',
                ['geo_id' => \App::user()->getRegion()->getId()],
                [
                    'product_list'  => $cart->getProductData(),
                    'service_list'  => [],
                    'warranty_list' => [],
                ],
                function ($data) use (&$result) {
                    $result = $data;
                },
                function(\Exception $e) use (&$result) {
                    \App::exception()->remove($e);
                    $result = $e;
                }
            );
            $client->execute();

            if ($result instanceof \Exception) {
                throw $result;
            }

            $result = array_merge([
                'sum' => 0,
            ], (array)$result);

            $productsInfo = [];
            foreach ($productsById as $product) {
                $cartProduct = $cart->getProductById($product->getId());
                $productInfo = [
                    'id'    => $product->getId(),
                    'name'  =>  $product->getName(),
                    'img'   =>  $product->getImageUrl(2),
                    'link'  =>  $product->getLink(),
                    'price' =>  $product->getPrice(),
                    'deleteUrl' => $cartProduct  ? (new \Helper\TemplateHelper())->url('cart.product.delete', ['productId' => $cartProduct->getId()]) : null,
                    'cartButton'     => [
                        'id' => \View\Id::cartButtonForProduct($product->getId()),
                    ],
                ];
                if (\App::config()->kissmentrics['enabled']) {
                    try {
                        $kissInfo = \Kissmetrics\Manager::getCartEvent($product);
                        $productInfo = array_merge($productInfo, $kissInfo['product']);
                    } catch (\Exception $e) {
                        \App::logger()->error($e, ['kissmetrics']);
                    }
                }

                $productsInfo[] = $productInfo;
            }

            $responseData = [
                'success' => true,
                'cart'    => [
                    'sum'           => $result['sum'],
                    'quantity'      => $quantity,
                    'full_quantity' => $cart->getProductsQuantity(),
                    'full_price'    => $cart->getSum(),
                    'old_price'     => $cart->getOriginalSum(),
                    'link'          => \App::router()->generate('order'),
                    'products'      => $cart->getProductsDumpNC(),
                ],
                'products'  => $productsInfo,
                'sender'    => $sender,
            ];

            $response = new \Http\JsonResponse($responseData);

            if ($cart->getSum()) {
                \Session\User::enableInfoCookie($response);
            } else {
                \Session\User::disableInfoCookie($response);
            }


        } catch(\Exception $e) {
            $responseData = [
                'success' => false,
                'data'    => ['error' => 'Не удалось добавить товар или услугу в корзину', 'debug' => $e->getMessage()],
            ];
            return new \Http\JsonResponse($responseData);
        }

        if (!$request->isXmlHttpRequest()) {
            return new \Http\RedirectResponse(\App::router()->generate('order'));
        }

        return $response;
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