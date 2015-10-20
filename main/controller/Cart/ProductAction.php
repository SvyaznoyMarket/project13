<?php

namespace Controller\Cart;

use EnterApplication\CurlTrait;
use EnterQuery as Query;
use Session\AbTest\ABHelperTrait;

class ProductAction {
    use CurlTrait, ABHelperTrait;

    /**
     * @deprecated Используйте setList
     * @param int           $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($productId, \Http\Request $request) {
        $productId = (int)$productId;
        $quantity = $request->get('quantity');

        $product = ['id' => $productId];
        if ($quantity === null) {
            $product['quantity'] = '+1';
            $product['up'] = '1';
        } else {
            $product['quantity'] = (int)$quantity;
        }

        $request->query->set('products', [$product]);
        $request->query->remove('quantity');
        $response = $this->setList($request);

        if ($response instanceof \Http\JsonResponse) {
            $data = $response->getData();
            $data['product'] = isset($data['setProducts'][0]) ? $data['setProducts'][0] : [];
            unset($data['setProducts']);
            $response->setData($data);
        }

        return $response;
    }

    /**
     * @deprecated Используйте setList
     * @param $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function delete(\Http\Request $request, $productId) {
        $request->query->set('quantity', 0);
        return $this->set($productId, $request);
    }

    /**
     * Принимает get параметры:
     * products[]['id'] Cм. параметр $setProducts[]['id'] метода \Session\Cart::update
     * products[]['ui'] Cм. параметр $setProducts[]['ui'] метода \Session\Cart::update
     * products[]['quantity'] Cм. параметр $setProducts[]['quantity'] метода \Session\Cart::update
     * products[]['up'] Cм. параметр $setProducts[]['up'] метода \Session\Cart::update
     * sender
     * sender2
     * credit
     * referer
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function setList(\Http\Request $request) {
        $cart = \App::user()->getCart();

        try {
            call_user_func(function() use(&$params, &$sender, &$request) {
                $sender = $request->query->get('sender');
                $sender2 = (string)$request->query->get('sender2');
                $params = [];

                $referer = $request->headers->get('referer') ?: '/';
                if (false === strpos($referer, \App::config()->mainHost)) {
                    $referer = '/';
                }
                // TODO ограничить допустимый размер
                $params['referer'] = $referer;

                if (is_string($sender) && !empty($sender)) {
                    $sender = ['name' => $sender];
                }

                if (!is_array($sender)) {
                    $sender = null;
                }

                if ($sender) {
                    // TODO ограничить допустимый размер
                    $params['sender'] = $sender;
                }

                if ($sender2) {
                    // TODO ограничить допустимый размер
                    $params['sender2'] = $sender2;
                }

                if ($request->query->get('credit') == 'on') {
                    $params['credit'] = ['enabled' => true];
                } else {
                    $params['credit'] = null;
                }
            });

            $setProducts = [];
            // TODO удалить $request->get('product') когда не останется таких запросов
            foreach ((array)$request->get('products', $request->get('product')) as $setProduct) {
                $setProducts[] = array_intersect_key($setProduct, ['id' => null, 'ui' => null, 'quantity' => null, 'up' => null]) + $params;
            }

            if (!$setProducts) {
                throw new \Exception('Не получен список товаров');
            }

            // вычисляем дельту между количеством старого продукта и нового
            $productsQuantityDelta = [];
            $productsInCart = \App::user()->getCart()->getProductsByUi();
            foreach ($setProducts as $setProd) {
                if (array_key_exists($setProd['ui'], $productsInCart)) {
                    if ($setProd['quantity'] === 0) {
                        $productsQuantityDelta[$setProd['ui']] = $productsInCart[$setProd['ui']]->quantity;
                    } else if (preg_match('/^[\+-]/', $setProd['quantity'])) {
                        $productsQuantityDelta[$setProd['ui']] = (int)$setProd['quantity'];
                    } else {
                        $productsQuantityDelta[$setProd['ui']] = (int)$setProd['quantity'] - $productsInCart[$setProd['ui']]->quantity;
                    }

                } else {
                    $productsQuantityDelta[$setProd['ui']] = (int)$setProd['quantity'];
                }
            }

            $updateResultProducts = $cart->update($setProducts, false, \App::config()->cart['productLimit']);

            $cart->pushStateEvent([]);
            
            // обновление серверной корзины
            call_user_func(function() use (&$updateResultProducts) {
                $userEntity = \App::user()->getEntity();
                if (!$this->isCoreCart() || !$userEntity) return;

                if ($userEntity = \App::user()->getEntity()) {
                    foreach ($updateResultProducts as $updateResultProduct) {
                        if ($updateResultProduct->setAction === 'delete') {
                            (new Query\Cart\RemoveProduct($userEntity->getUi(), $updateResultProduct->cartProduct->ui))->prepare();
                        } else {
                            (new Query\Cart\SetProduct($userEntity->getUi(), $updateResultProduct->cartProduct->ui, $updateResultProduct->cartProduct->quantity))->prepare();
                        }
                    }

                    $this->getCurl()->execute();
                }
            });

            $kitProduct = null;
            if (!empty($request->query->get('kitProduct')['ui'])) {
                /** @var \Model\Product\Entity[] $kitProducts */
                $kitProducts = [new \Model\Product\Entity(['ui' => $request->query->get('kitProduct')['ui']])];
                \RepositoryManager::product()->prepareProductQueries($kitProducts);
                \App::coreClientV2()->execute();
                if ($kitProducts) {
                    $kitProduct = [
                        'name' =>  $kitProducts[0]->getName(),
                        'article' => $kitProducts[0]->getArticle(),
                        'price' => $kitProducts[0]->getPrice(),
                    ];
                }
            }

            $response = [
                'success' => true,
                'cart'    => $cart->getDump(),
                // Содержит товары из корзины и удалённые товары
                'setProducts'  => array_values(array_filter(array_map(function(\Session\Cart\Update\Result\Product $updateResultProduct) use ($productsQuantityDelta) {
                    if (!$updateResultProduct->setAction) {
                        return;
                    }
                    
                    return [
                        'id'        => $updateResultProduct->cartProduct->id,
                        'article'   => $updateResultProduct->cartProduct->article,
                        'name'      => $updateResultProduct->cartProduct->name,
                        'img'       => $updateResultProduct->cartProduct->image,
                        'link'      => $updateResultProduct->cartProduct->url,
                        'price'     => $updateResultProduct->cartProduct->price,
                        'cartButton'     => [
                            'id' => \View\Id::cartButtonForProduct($updateResultProduct->cartProduct->id),
                        ],
                        'isTchiboProduct' => $updateResultProduct->cartProduct->rootCategory && 'Tchibo' === $updateResultProduct->cartProduct->rootCategory->name,
                        // На данный момент есть лишь одно заглушённое использование category в https://github.com/SvyaznoyMarket/project13/blob/a61ce6c8a90be2a3b65af544983628892607b18c/web/js/dev/ports/_insider.js#L27
                        // 'category'        => $this->getCategories($product),
                        'isSlot' => $updateResultProduct->cartProduct->isSlot,
                        'isOnlyFromPartner' => $updateResultProduct->cartProduct->isOnlyFromPartner,
                        'quantity'          => $updateResultProduct->cartProduct->quantity,
                        'quantityDelta'     => array_key_exists($updateResultProduct->cartProduct->ui, $productsQuantityDelta) ? $productsQuantityDelta[$updateResultProduct->cartProduct->ui] : false,
                        'categoryName'      => $updateResultProduct->fullProduct && $updateResultProduct->fullProduct->getRootCategory() ? $updateResultProduct->fullProduct->getRootCategory()->getName() : '',
                        'brand'             => $updateResultProduct->fullProduct && $updateResultProduct->fullProduct->getBrand() ? $updateResultProduct->fullProduct->getBrand()->getName() : '',
                    ];
                }, $updateResultProducts))),
                'kitProduct' => $kitProduct,
                'sender' => $sender,
            ];
            
            // TODO удалить, когда не останется запросов с $request->get('product')
            if ($request->get('product')) {
                $response['products'] = $response['setProducts'];
                unset($response['setProducts']);
            }

            // Немного отформатируем
            if (\App::config()->lite['enabled']) {
                $response['cart']['fullQuantity'] = count($cart->getProductsById());
            }
            
            $response = new \Http\JsonResponse($response);
        } catch(\Session\CartProductLimitException $e) {
            $response = new \Http\JsonResponse([
                'success' => false,
                'noticePopupHtml' => \App::mustache()->render('notice-popup', [
                    'text' => 'Из-за большого количества заказов ограничено количество товаров в корзине. Максимальное количество — ' . $e->productLimit . ' шт.',
                ]),
            ]);
        } catch(\Exception $e) {
            $response = new \Http\JsonResponse([
                'success' => false,
                'data'    => ['error' => 'Не удалось добавить товар или услугу в корзину', 'debug' => $e->getMessage()],
            ]);
        }

        if (!$request->isXmlHttpRequest()) {
            return new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('order'));
        }

        return $response;
    }

    /*
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
    */
}