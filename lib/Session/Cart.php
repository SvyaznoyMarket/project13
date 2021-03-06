<?php

namespace Session {
    use EnterApplication\CurlTrait;
    use Model\Product\Category\Entity;

    class Cart {
        use CurlTrait;
    
        /** @var string Сессионное имя новой корзины */
        private $sessionName;
        /** @var \Http\Session */
        private $storage;

        public function __construct() {
            $this->sessionName = \App::config()->cart['sessionName'] ?: 'cart';
            $this->storage = \App::session();

            // очистить старую корзину
            $this->storage->remove('userCart');

            try {
                $this->update();
            } catch(\Exception $e) {}
        }
    
        public function clear() {
            $this->setSessionCart(null);
        }

        /**
         * ВНИМАНИЕ! Перед редактирование кода данного метода ознакомьтесь с комментариями к методу self::setSessionCart
         *
         * Медот актулизирует данные товаров в корзине, при необходимости добавляя/изменяя/удаляя товары из
         * $setProducts. Данные в сессию будут сохранены только, если метод был выполнен без ошибок.
         *
         * TODO заменить в маршрутах cart.product.setList id на ui
         * TODO заменить в вызовах данного метода $setProducts[]['id'] на $setProducts[]['ui']
         *
         * @param array      $setProducts Массив вида [['id' => '12345', 'quantity' => '10', 'up' => '1'], ...]
         * @param string|int $setProducts[]['id'] Идентификатор товара
         * @param string     $setProducts[]['ui'] Идентификатор товара
         * @param string|int $setProducts[]['quantity'] Кол-во товара. Примеры допустимых значений:
         *                                                '0' - товар будет удалён
         *                                               '10' - будет установлено указанное кол-во (если товар уже
         *                                                      существует в корзине, то указанное кол-во перезапишет
         *                                                      существующее)
         *                                              '+10' - будет установлено указанное кол-во (если товар уже
         *                                                      существует в корзине, то указанное кол-во будет
         *                                                      добавлено к существующему)
         *                                              '-10' - если товар уже существует в корзине, то существующее
         *                                                      кол-во будет уменьшено на указанное (притом, если в
         *                                                      результате уменьшения кол-во станет <= 0, то оно будет
         *                                                      установлено в 1; если товара не существует в корзине, то
         *                                                      товар добавлен не будет
         * @param bool|string|int $setProducts[]['up'] Если true и товар уже существует в корзине, то он будет перемещён в начало
         * @param mixed           $setProducts[]['sender']
         * @param mixed           $setProducts[]['sender2']
         * @param mixed           $setProducts[]['credit']
         * @param mixed           $setProducts[]['referer']
         * @param bool $forceUpdate Актуализировать данные в корзине вне зависимости от времени последнего обновления
         *                          и $setProducts
         * @param int $productLimit Макс. кол-во товаров в корзине, при достижении которого новые товары не будут
         *                          добавляться по одному за раз (но по несколько за раз - будут, т.к. это нужно для
         *                          полноценного добавления товаров из набора)
         *
         * @throws \Exception Если обновление корзины не удалось выполнить
         * @throws \Session\CartProductLimitException Если добавляется один новый товар и кол-во товаров в корзине уже
         *                                            больше или равно $productLimit
         *
         * @return \Session\Cart\Update\Result\Product[] Все товары корзины, включая удалённые через $setProducts товары
         *                                               (если какие-то товары из $setProducts фактически не изменили
         *                                               содержимое корзины, то они не будут возвращены; если фактически
         *                                               изменения корзины не произошло (не из-за ошибок), то будет
         *                                               возвращён пустой массив). Для удалённых товаров
         *                                               cartProduct->quantity будет содержать значение до удаления.
         */
        public function update(array $setProducts = [], $forceUpdate = false, $productLimit = 0) {
            try {
                $resultProducts = [];
                $sessionCart = $this->getSessionCart();
                /** @var \Model\Product\Entity[] $backendProductsByUi */
                $backendProductsByUi = [];
                /** @var \Model\Product\Entity[] $backendProductsById */
                $backendProductsById = [];

                // Фильтруем и форматируем $setProducts
                call_user_func(function() use(&$setProducts) {
                    foreach ($setProducts as $key => $setProduct) {
                        if (empty($setProduct['id']) && empty($setProduct['ui'])) {
                            \App::logger()->error(['message' => 'Не указан id и ui товара'], ['cart/update']);
                            unset($setProducts[$key]);
                            continue;
                        }

                        $setProducts[$key]['id'] = !empty($setProduct['id']) ? (int)$setProduct['id'] : null;
                        $setProducts[$key]['ui'] = !empty($setProduct['ui']) ? (string)$setProduct['ui'] : null;
                        $setProducts[$key]['quantity'] = isset($setProduct['quantity']) ? (string)$setProduct['quantity'] : '';
                        $setProducts[$key]['up'] = isset($setProduct['up']) ? (bool)$setProduct['up'] : false;
                    }
                });

                if (!$setProducts && !$this->hasSessionProductWithoutExpectedData() && !($sessionCart['product'] && ($forceUpdate || $this->isExpired()))) {
                    return $resultProducts;
                }

                // Получение данные от бэкэнда для $setProducts товаров и сессионных товаров
                call_user_func(function() use(&$backendProductsByUi, &$backendProductsById, $setProducts, $sessionCart) {
                    foreach ($setProducts as $setProduct) {
                        if (!empty($setProduct['ui'])) {
                            $backendProductsByUi[$setProduct['ui']] = new \Model\Product\Entity(['ui' => $setProduct['ui']]);
                        } else if (!empty($setProduct['id'])) {
                            $backendProductsById[$setProduct['id']] = new \Model\Product\Entity(['id' => $setProduct['id']]);
                        }
                    }

                    foreach ($sessionCart['product'] as $sessionProduct) {
                        if (!empty($sessionProduct['ui'])) {
                            $backendProductsByUi[$sessionProduct['ui']] = new \Model\Product\Entity(['ui' => $sessionProduct['ui']]);
                        } else if (!empty($sessionProduct['id'])) {
                            $backendProductsById[$sessionProduct['id']] = new \Model\Product\Entity(['id' => $sessionProduct['id']]);
                        }
                    }

                    $exceptionCount = count(\App::exception()->all());
                    \RepositoryManager::product()->prepareProductQueries($backendProductsByUi, 'media category');
                    \RepositoryManager::product()->prepareProductQueries($backendProductsById, 'media category');
                    \App::coreClientV2()->execute();

                    if (count(\App::exception()->all()) > $exceptionCount && \App::exception()->last()) {
                        throw new \Exception(\App::exception()->last()->getMessage());
                    }
                });

                $setProductResultActionsById = [];
                // Добавление/замена/удаление $setProducts товаров в $sessionCart
                call_user_func(function() use(&$sessionCart, &$setProductResultActionsById, $setProducts, $backendProductsByUi, $backendProductsById) {
                    $sessionProductsByUi = [];
                    foreach ($sessionCart['product'] as $sessionProduct) {
                        $sessionProductsByUi[$sessionProduct['ui']] = $sessionProduct;
                    }

                    foreach ($setProducts as $key => $setProduct) {
                        // Удаление товара. Должно происходить вне зависимости то того, вернул ли бэкэнд товар или нет
                        // (иначе у пользователя не будет возможности удалить из корзины товар, который был удалён (из ядра
                        // или scms) или заблокирован (в scms))
                        if ((string)$setProduct['quantity'] === '0') {
                            call_user_func(function() use(&$setProductResultActionsById, $setProduct, $sessionCart, $sessionProductsByUi) {
                                $sessionProduct = null;
                                if (!empty($setProduct['ui']) && isset($sessionProductsByUi[$setProduct['ui']])) {
                                    $sessionProduct = $sessionProductsByUi[$setProduct['ui']];
                                } else if (!empty($setProduct['id']) && isset($sessionCart['product'][$setProduct['id']])) {
                                    $sessionProduct = $sessionCart['product'][$setProduct['id']];
                                }

                                if ($sessionProduct) {
                                    // Обратите внимание, что $setProduct из следующей итерации может изменить action для
                                    // данного товара на совершенно другой
                                    $setProductResultActionsById[$sessionProduct['id']] = 'delete';
                                }
                            });

                            continue;
                        }

                        $backendProduct = null;
                        if (!empty($setProduct['ui']) && isset($backendProductsByUi[$setProduct['ui']])) {
                            $backendProduct = $backendProductsByUi[$setProduct['ui']];
                        } else if (!empty($setProduct['id']) && isset($backendProductsById[$setProduct['id']])) {
                            $backendProduct = $backendProductsById[$setProduct['id']];
                        }

                        // Если бэкэнд не вернул товар и не было ошибок запроса, то это означает, что товары были
                        // удалены (из ядра или scms) или заблокированы (в scms)
                        if (!$backendProduct) {
                            continue;
                        }

                        // В сессию должны попадать лишь товары и с id и с ui
                        if (empty($backendProduct->id) || empty($backendProduct->ui)) {
                            \App::logger()->error(['message' => 'Не получены id или ui товара от бэкэнда (id товара из параметров: ' . $setProduct['id'] . ', ui товара из параметров: ' . $setProduct['ui'] . ', id товара из бэкэнда: ' . $backendProduct->id . ', ui товара из бэкэнда: ' . $backendProduct->ui . ')'], ['cart/update']);
                            continue;
                        }

                        if (isset($sessionCart['product'][$backendProduct->id])) {
                            $newSessionProduct = $sessionCart['product'][$backendProduct->id];
                            // Обратите внимание, что $setProduct из следующей итерации может изменить action для
                            // данного товара на совершенно другой
                            $setProductResultActionsById[$backendProduct->id] = 'replace';
                        } else {
                            $newSessionProduct = array_merge(['quantity' => 0], $this->createSessionProductFromBackendProduct($backendProduct));
                            // Обратите внимание, что $setProduct из следующей итерации может изменить action для
                            // данного товара на совершенно другой
                            $setProductResultActionsById[$backendProduct->id] = 'add';
                        }

                        $newSessionProduct = array_merge($newSessionProduct, [
                            'sender' => isset($setProduct['sender']) ? $setProduct['sender'] : null,
                            'sender2' => isset($setProduct['sender2']) ? $setProduct['sender2'] : null,
                            'credit' => isset($setProduct['credit']) ? $setProduct['credit'] : null,
                            'referer' => isset($setProduct['referer']) ? $setProduct['referer'] : null,
                        ]);

                        if (preg_match('/^([\+\-])?(\d+)$/s', $setProduct['quantity'], $matches)) {
                            if ($matches[1] === '+') {
                                $newSessionProduct['quantity'] += $matches[2];
                            } else if ($matches[1] === '-') {
                                $newSessionProduct['quantity'] -= $matches[2];

                                // SITE-5957 В расширенной корзине при уменьшении кол-ва до нуля товар не должен удаляться
                                if ($newSessionProduct['quantity'] <= 0) {
                                    $newSessionProduct['quantity'] = 1;
                                }
                            } else {
                                $newSessionProduct['quantity'] = (int)$matches[2];
                            }
                        } else {
                            $newSessionProduct['quantity'] += 1;
                        }

                        if ($newSessionProduct['quantity'] <= 0) {
                            // Обратите внимание, что $setProduct из следующей итерации может изменить action для
                            // данного товара на совершенно другой
                            $setProductResultActionsById[$backendProduct->id] = 'delete';
                            continue;
                        }

                        if (\App::config()->cart['checkStock'] && $backendProduct->getStock()) {
                            if ($newSessionProduct['quantity'] > $backendProduct->getStockWithMaxQuantity()->getQuantity()) {
                                // TODO может вместо выброса исключения сохранять у товара в сессии значение maxQuantity (и в корзине при привышении quantity над maxQuantity выводить надпись вроде "указанного кол-ва нет в наличии, максимально доступно: 25 шт.")?
                                throw new \Exception('Нет запрошенного количества товара');
                            }
                        }

                        if (!isset($newSessionProduct['added'])) {
                            $newSessionProduct['added'] = date('c');
                        }

                        // https://jira.enter.ru/browse/SITE-5022?focusedCommentId=153694&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#comment-153694
                        if ($setProduct['up']) {
                            unset($sessionCart['product'][$backendProduct->id]);
                        }

                        $sessionCart['product'][$backendProduct->id] = $newSessionProduct;
                    }
                });

                // Обновление сессионных товаров
                call_user_func(function() use(&$sessionCart, &$resultProducts, $setProductResultActionsById, $backendProductsByUi, $backendProductsById) {
                    foreach ($sessionCart['product'] as $key => $sessionProduct) {
                        $backendProduct = null;
                        if (!empty($sessionProduct['ui']) && isset($backendProductsByUi[$sessionProduct['ui']])) {
                            $backendProduct = $backendProductsByUi[$sessionProduct['ui']];
                        } else if (!empty($sessionProduct['id']) && isset($backendProductsById[$sessionProduct['id']])) {
                            $backendProduct = $backendProductsById[$sessionProduct['id']];
                        }

                        // Удаление товара. Должно происходить вне зависимости то того, вернул ли бэкэнд товар или нет
                        // (иначе у пользователя не будет возможности удалить из корзины товар, который был удалён (из ядра
                        // или scms) или заблокирован (в scms))
                        if (isset($setProductResultActionsById[$sessionProduct['id']]) && $setProductResultActionsById[$sessionProduct['id']] === 'delete') {
                            $resultProduct = new \Session\Cart\Update\Result\Product();
                            $resultProduct->cartProduct = new \Model\Cart\Product\Entity($sessionProduct);
                            $resultProduct->fullProduct = $backendProduct;
                            $resultProduct->setAction = $setProductResultActionsById[$sessionProduct['id']];
                            $resultProducts[] = $resultProduct;

                            unset($sessionCart['product'][$key]);
                            continue;
                        }

                        if ($backendProduct) {
                            $sessionCart['product'][$key] = array_merge($sessionProduct, $this->createSessionProductFromBackendProduct($backendProduct));
                            $sessionCart['product'][$key]['isGone'] = false;
                        } else {
                            // Если бэкэнд не вернул товар и не было ошибок запроса, то это означает, что товары были
                            // удалены (из ядра или scms) или заблокированы (в scms)
                            $sessionCart['product'][$key]['isGone'] = true;
                        }

                        $resultProduct = new \Session\Cart\Update\Result\Product();
                        $resultProduct->cartProduct = new \Model\Cart\Product\Entity($sessionProduct);
                        $resultProduct->fullProduct = $backendProduct;
                        if (isset($setProductResultActionsById[$sessionProduct['id']])) {
                            $resultProduct->setAction = $setProductResultActionsById[$sessionProduct['id']];
                        }

                        $resultProducts[] = $resultProduct;
                    }
                });

                // Проверка на ограничение кол-ва добавляемых товаров
                call_user_func(function() use($productLimit, $sessionCart, $setProductResultActionsById) {
                    if (!$productLimit) {
                        return;
                    }

                    $addProductCount = count(array_filter($setProductResultActionsById, function($action) {
                        return $action === 'add';
                    }));

                    // Ограничивает добавление только одиночных товаров для возможности полноценного добавления товаров
                    // из набор пакетов
                    if ($addProductCount == 1 && count($this->excludeGoneSessionProducts($sessionCart['product'])) > $productLimit) {
                        $e = new \Session\CartProductLimitException('Превышен лимит на добавление в корзину новых товаров');
                        $e->productLimit = $productLimit;
                        throw $e;
                    }
                });

                // Переиндексируем массив для исправления возможных ошибок
                call_user_func(function() use(&$sessionCart) {
                    $sessionProductsById = [];
                    foreach ($sessionCart['product'] as $key => $sessionProduct) {
                        if ((string)$key !== (string)$sessionProduct['id']) {
                            \App::logger()->warn(['message' => 'У сессионного товара (id: ' . $sessionProduct['id'] . ', ui: ' . $sessionProduct['ui'] . ') значение элемента id не совпадает с ключом элемента (' . $key . '), ключ будет обновлён'], ['cart/update']);
                        }

                        $sessionProductsById[$sessionProduct['id']] = $sessionProduct;
                    }

                    $sessionCart['product'] = $sessionProductsById;
                });

                // Обновление цен
                call_user_func(function() use(&$sessionCart) {
                    if (!$sessionCart['product']) {
                        return;
                    }

                    $isPriceUpdated = false;
                    // Для корректного подсчёта суммы следует вызывать данный метод лишь после добавления/замены/удаления
                    // $setProducts товаров и обновления товаров в сессии
                    \App::coreClientV2()->addQuery(
                        'cart/get-price',
                        ['geo_id' => \App::user()->getRegion()->getId()],
                        [
                            'product_list'  => array_map(function($product) {
                                return [
                                    'id'       => $product['id'],
                                    'quantity' => $product['quantity'],
                                ];
                            }, $this->excludeGoneSessionProducts($sessionCart['product'])),
                        ],
                        function ($response) use (&$sessionCart, &$isPriceUpdated) {
                            if (!isset($response['product_list'])) {
                                return;
                            }

                            $isPriceUpdated = true;

                            if (is_array($response['product_list'])) {
                                foreach ($response['product_list'] as $item) {
                                    if (isset($item['id']) && isset($sessionCart['product'][$item['id']])) {
                                        $sessionCart['product'][$item['id']]['price'] = isset($item['price']) ? (float)$item['price'] : 0;
                                        $sessionCart['product'][$item['id']]['sum'] = isset($item['sum']) ? (float)$item['sum'] : 0;
                                    }
                                }
                            }

                            $sessionCart['sum'] = isset($response['sum']) ? (float)$response['sum'] : 0;
                        }
                    );

                    $exceptionCount = count(\App::exception()->all());
                    \App::coreClientV2()->execute();

                    if (count(\App::exception()->all()) > $exceptionCount && \App::exception()->last()) {
                        throw new \Exception(\App::exception()->last()->getMessage());
                    }

                    if (!$isPriceUpdated) {
                        throw new \Exception('Не удалось получить цены для товаров');
                    }
                });

                $sessionCart['updated'] = (new \DateTime('now'))->format('c');

                // метка обновления ядерной корзины
                if (!array_key_exists('coreUpdated', $sessionCart)) {
                    $sessionCart['coreUpdated'] = null;
                }

                $this->setSessionCart($sessionCart);

                // TODO может перенести синхронизацию серверной корзины сюда (выполняя её на основе данных из $resultProducts)?

                return $resultProducts;
            } catch(\Session\CartProductLimitException $e) {
                throw $e;
            } catch(\Exception $e) {
                \App::logger()->error(['message' => 'Не удалось обновить корзину', 'error' => $e, 'sender' => __FILE__ . ' ' . __LINE__], ['cart/update']);
                throw $e;
            }
        }
    
        /**
         * @return int
         */
        public function count() {
            return count($this->excludeGoneSessionProducts($this->getSessionCart()['product']));
        }
    
        /**
         * @return \Model\Cart\Product\Entity[]
         */
        public function getProductsById() {
            $cartProducts = [];
            foreach ($this->excludeGoneSessionProducts($this->getSessionCart()['product']) as $sessionProduct) {
                $cartProducts[$sessionProduct['id']] = new \Model\Cart\Product\Entity($sessionProduct);
            }
    
            return $cartProducts;
        }
        
        /**
         * @return \Model\Cart\Product\Entity[]
         */
        public function getProductsByUi() {
            $cartProducts = [];
            foreach ($this->excludeGoneSessionProducts($this->getSessionCart()['product']) as $sessionProduct) {
                $cartProducts[$sessionProduct['ui']] = new \Model\Cart\Product\Entity($sessionProduct);
            }
    
            return $cartProducts;
        }
    
        /**
         * @param $productId
         * @return bool
         */
        public function hasProduct($productId) {
            foreach ($this->excludeGoneSessionProducts($this->getSessionCart()['product']) as $product) {
                if ($product['id'] == $productId) {
                    return true;
                }
            }
    
            return false;
        }

        /**
         * @param int $productId
         * @return int
         */
        public function getProductQuantity($productId) {
            $productId = (int)$productId;
            foreach ($this->excludeGoneSessionProducts($this->getSessionCart()['product']) as $product) {
                if ($product['id'] == $productId) {
                    return (int)$product['quantity'];
                }
            }

            return 0;
        }

        /**
         * @param string $productUi
         * @return int
         */
        public function getProductQuantityByUi($productUi) {
            $productUi = (string)$productUi;
            foreach ($this->excludeGoneSessionProducts($this->getSessionCart()['product']) as $product) {
                if ($product['ui'] === $productUi) {
                    return (int)$product['quantity'];
                }
            }

            return '';
        }
    
        /**
         * Возвращает массив id продуктов, добавленных в кредит (или пустой массив)
         * @return array
         */
        public function getCreditProductIds(){
            $ids = [];
            foreach ($this->excludeGoneSessionProducts($this->getSessionCart()['product']) as $product) {
                if (isset($product['credit']['enabled']) && (true == $product['credit']['enabled'])) $ids[] = $product['id'];
            }
            return $ids;
        }
    
        /**
         * @return int
         */
        public function getSum() {
            return $this->getSessionCart()['sum'];
        }
    
        /**
         * @return array
         */
        public function getDump() {
            $sessionCart = $this->getSessionCart();
            $helper = \App::helper();
    
            return [
                'products' => array_values(array_map(function($cartProduct) use($helper) {
                    return [
                        'id'                 => $cartProduct['id'],
                        'ui'                 => $cartProduct['ui'],
                        'article'            => $cartProduct['article'],
                        'name'               => $cartProduct['name'],
                        'price'              => $cartProduct['price'],
                        'formattedPrice'     => $helper->formatPrice($cartProduct['price']),
                        'formattedFullPrice' => $helper->formatPrice($cartProduct['price'] * $cartProduct['quantity']),
                        'quantity'           => $cartProduct['quantity'],
                        'link'               => $cartProduct['url'],
                        'img'                => $cartProduct['image'],
                        'cartButton'         => ['id' => \View\Id::cartButtonForProduct($cartProduct['id'])],
                        'category'           => $cartProduct['category'],
                        'rootCategory'       => $cartProduct['rootCategory'],
                        'isCredit'           => isset($cartProduct['credit']['enabled']) && ($cartProduct['credit']['enabled'] === true),
                        'isAvailable'        => isset($cartProduct['isAvailable']) ? $cartProduct['isAvailable'] : true,
                        'deleteUrl'          => $helper->url('cart.product.setList', ['products' => [['ui' => $cartProduct['ui'], 'quantity' => '0']]]),
                        'decreaseUrl'        => $helper->url('cart.product.setList', ['products' => [['ui' => $cartProduct['ui'], 'quantity' => '-1']]]),
                        'increaseUrl'        => $helper->url('cart.product.setList', ['products' => [['ui' => $cartProduct['ui'], 'quantity' => '+1']]]),
                        'sender'             => $cartProduct['sender']
                    ];
                }, $this->excludeGoneSessionProducts($sessionCart['product']))),
                'sum' => $sessionCart['sum'],
                'link' => \App::router()->generateUrl('orderV3'),
            ];
        }

        /**
         * @return \DateTime|null
         */
        public function getCoreUpdated() {
            $sessionCart = $this->getSessionCart();

            $date = isset($sessionCart['coreUpdated']) ? $sessionCart['coreUpdated'] : null;
            if ($date) {
                try {
                    $date = new \DateTime($sessionCart['coreUpdated']);
                } catch (\Exception $e) {}
            }

            return $date;
        }

        /**
         * @param \DateTime|null $date
         */
        public function setCoreUpdated(\DateTime $date = null) {
            $sessionCart = $this->getSessionCart();

            $sessionCart['coreUpdated'] = $date ? $date->format('Y-m-d H:i:s') : null;
            $this->setSessionCart($sessionCart);
        }
    
        /**
         * @param array $data
         */
        public function pushStateEvent(array $data) {
            try {
                $userEntity = \App::user()->getEntity();
                if (!$userEntity) {
                    return;
                }
    
                $data = array_replace_recursive([
                    'user' => [
                        'uid' => $userEntity ? $userEntity->getUi() : null,
                    ],
                    'session_id'  => \App::session()->getId(),
                    'cart'        => [
                        'products' => array_map(
                            function ($item) {
                                return [
                                    'uid'      => $item['ui'],
                                    'quantity' => $item['quantity'],
                                ];
                            },
                            $this->excludeGoneSessionProducts($this->getSessionCart()['product'])
                        ),
                        'sum'     => $this->getSessionCart()['sum'],
                    ],
                ], $data);
                (new \EnterQuery\Event\PushCartState($data))->prepare();
    
                $this->getCurl()->execute();
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e], ['cart/event']);
            }
        }

        private function hasSessionProductWithoutExpectedData() {
            $sessionCart = $this->getSessionCart();
            $expectedSessionProductStub = $this->createSessionProductFromBackendProduct(new \Model\Product\Entity());
            foreach ($this->excludeGoneSessionProducts($sessionCart['product']) as $sessionProduct) {
                if (!$this->isActualLikeExpectedArray($sessionProduct, $expectedSessionProductStub)) {
                    return true;
                }
            }

            return false;
        }

        private function isActualLikeExpectedArray($actual, array $expected) {
            if (!is_array($actual)) {
                return false;
            }

            foreach ($expected as $expectedKey => $expectedValue) {
                if (!isset($actual[$expectedKey])) {
                    return false;
                }

                if (is_array($expectedValue) && !$this->isActualLikeExpectedArray($actual[$expectedKey], $expectedValue)) {
                    return false;
                }
            }

            return true;
        }
    
        private function isExpired() {
            try {
                $sessionCart = $this->getSessionCart();
                if ($sessionCart['updated']) {
                    $updatedTime = new \DateTime($sessionCart['updated']);
                    return !$updatedTime || $updatedTime->diff(new \DateTime('now'), true)->i > (\App::config()->cart['updateTime'] ?: 1); // больше n-минут
                }
            } catch (\Exception $e) {}
    
            return true;
        }
        
        private function createSessionProductFromBackendProduct(\Model\Product\Entity $backendProduct) {

            $categoryNames = array_map(function(Entity $category) { return $category->name; }, $backendProduct->getCategory() );

            return [
                'id'                => (int)$backendProduct->id,
                'ui'                => (string)$backendProduct->ui,
                'article'           => (string)$backendProduct->getArticle(),
                'barcode'           => (string)$backendProduct->barcode,
                'name'              => (string)$backendProduct->getName(),
                'brandName'         => $backendProduct->getBrand() ? (string)$backendProduct->getBrand()->getName() : '',
                'price'             => (float)$backendProduct->getPrice(),
                'image'             => (string)$backendProduct->getMainImageUrl('product_120'),
                'url'               => (string)$backendProduct->getLink(),
                'isSlot'            => (bool)$backendProduct->getSlotPartnerOffer(),
                'isOnlyFromPartner' => (bool)$backendProduct->isOnlyFromPartner(),
                'isAvailable'       => (bool)$backendProduct->isAvailable(),
                'rootCategory' => [
                    'id'    => $backendProduct->getRootCategory() ? (int)$backendProduct->getRootCategory()->getId() : 0,
                    'name'  => $backendProduct->getRootCategory() ? (string)$backendProduct->getRootCategory()->getName() : ''
                ],
                'category'     => [
                    'id'    => $backendProduct->getParentCategory() ? (int)$backendProduct->getParentCategory()->getId() : 0,
                    'name'  => $backendProduct->getParentCategory() ? (string)$backendProduct->getParentCategory()->getName() : ''
                ],
                'categoryPath'  => (string)implode(' / ', $categoryNames)
            ];
        }
        
        private function excludeGoneSessionProducts(array $sessionProducts) {
            return array_filter($sessionProducts, function($sessionProduct) {
                return !$sessionProduct['isGone'];
            });
        }

        private function getSessionCart() {
            $sessionCart = $this->storage->get($this->sessionName);
    
            if (!isset($sessionCart['product']) || !is_array($sessionCart['product'])) {
                $sessionCart['product'] = [];
            }
    
            $sessionProductStub = $this->createSessionProductFromBackendProduct(new \Model\Product\Entity());
            array_walk_recursive($sessionProductStub, function(&$value){
                $value = null;
            });
            
            foreach ($sessionCart['product'] as $key => $sessionProduct) {
                if (empty($sessionProduct['id']) && empty($sessionProduct['ui'])) {
                    // Поскольку метод update берёт данные из этого метода, такие левые товары пропадут из сессии при
                    // следующем успешном вызове update
                    unset($sessionCart['product'][$key]);
                } else {
                    // См. описание одноимённых свойств класса \Model\Cart\Product\Entity
                    $sessionCart['product'][$key] = $sessionProduct + $sessionProductStub + [
                        'sender' => null,
                        'sender2' => null,
                        'credit' => null,
                        'referer' => null,
                        'quantity' => 0,
                        'isGone' => false,
                        'added' => null,
                    ];
                }
            }

            if (!isset($sessionCart['sum']) || !$sessionCart['product']) {
                $sessionCart['sum'] = 0;
            }
    
            $sessionCart += ['updated' => null];

            return $sessionCart;
        }
    
        private function setSessionCart($sessionCart) {
            // ВНИМАНИЕ!
            // Данный метод изменяет сессионные данные, с которыми работает как www.enter.ru так и m.enter.ru, поэтому
            // необходимо обеспечивать совместимость данных сессии с кодом m.enter.ru
            
            // TODO удалять незаполненные элементы для экономии места в сессиях
            $this->storage->set($this->sessionName, $sessionCart);
        }
    }
}

namespace Session\Cart\Update\Result {
    class Product {
        /** @var \Model\Cart\Product\Entity */
        public $cartProduct;
        /**
         * Будет задано, если бэкэнд вернул данные о товаре (чего может не произойти, если товар был удалён (из ядра или
         * scms) или заблокирован (в scms))
         * @var \Model\Product\Entity|null
         */
        public $fullProduct;
        /**
         * Возможные значения:
         *     'add' - товар из $setProducts был добавлен в корзину, до этого такого товара в корзине не было
         * 'replace' - товар из $setProducts заменил товар, который до этого был в корзине
         *  'delete' - товар из $setProducts был удалён из корзины
         *        '' - товар не был в отфильтрованном списке $setProducts
         * @var string 
         */
        public $setAction = '';
    }
}