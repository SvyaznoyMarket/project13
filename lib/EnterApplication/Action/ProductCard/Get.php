<?php

namespace EnterApplication\Action\ProductCard {

    use EnterApplication\Action\ProductCard\Get\Request;
    use EnterQuery as Query;

    class Get {
        use \EnterApplication\CurlTrait;

        public function execute(Request $request)
        {
            $startAt = microtime(true);

            $curl = $this->getCurl();

            // товар
            $productQuery = null;
            if ($request->productCriteria['token']) {
                $productQuery = new Query\Product\GetByToken($request->productCriteria['token'], $request->regionId);
            }
            if (!$productQuery) {
                throw new \InvalidArgumentException('Неверный критерий получения товара');
            }

            // доставка и способы оплаты
            $deliveryError = null;
            $productQuery->prepare($productError, function() use (
                &$productQuery,
                &$deliveryError,
                &$paymentGroupError
                //&$deliveryQuery,
            ) {
                $productId = (string)$productQuery->response->product['id'];
                if (!$productId) {
                    return;
                }

                // доставка
                try {
                    $deliveryQuery = new Query\Delivery\GetByCart();
                    // корзина
                    $deliveryQuery->cart->products[] = $deliveryQuery->cart->createProduct($productId, 1);
                    // регион
                    $deliveryQuery->regionId = $productQuery->regionId;

                    $deliveryQuery->prepare($deliveryError);
                } catch (\Exception $e) {
                    $deliveryError = $e;
                }

                // методы оплаты
                try {
                    $paymentGroupQuery = new Query\PaymentGroup\GetByCart();
                    // корзина
                    $paymentGroupQuery->cart->products[] = $paymentGroupQuery->cart->createProduct($productId, 1);
                    // регион
                    $paymentGroupQuery->regionId = $productQuery->regionId;
                    // фильтер
                    $paymentGroupQuery->filter->isCorporative = false;
                    $paymentGroupQuery->filter->isCredit = true;

                    $paymentGroupQuery->prepare($paymentGroupError);
                } catch (\Exception $e) {
                    $paymentGroupError = $e;
                }
            });

            // редирект
            $redirectQuery = (new Query\Redirect\GetByUrl($request->urlPath))->prepare($redirectError); // TODO: throw Exception

            // аб-тест
            $abTestQuery = (new Query\AbTest\GetActive())->prepare($abTestError);

            // регион
            $regionQuery = (new Query\Region\GetById($request->regionId))->prepare($abTestError);

            // выполнение запросов
            $curl->execute();

            //var_dump($paymentGroupError);

            die(microtime(true) - $startAt);
        }

        /**
         * @return Request
         */
        public function createRequest()
        {
            return new Request();
        }

        public function executeV1() {
            $startAt = microtime(true);

            $curl = $this->getCurl();

            // редирект
            $this->pushQuery('http://scms.enter.ru/seo/redirect?from_url=/product/children/pedalnaya-mashina-bugati-disney-tachki-2010108013848',
                [], $redirect
            );

            // аб-тест
            $this->pushQuery('http://scms.enter.ru/api/ab_test/get-active', [], $abTest
            );

            // товар
            $delivery = null;
            $paymentMethod = null;
            $this->pushQuery('http://api.enter.ru/v2/product/get?select_type=slug&slug=pedalnaya-mashina-bugati-disney-tachki-2010108013848&geo_id=14974&client_id=site',
                [], $product, $productError, function () use (&$product, &$delivery, &$paymentMethod) {
                    $this->pushQuery('http://api.enter.ru/v2/delivery/calc2?geo_id=14974&client_id=site', [
                            'product_list' => [
                                [
                                    'id'       => $product['result'][0]['id'],
                                    'quantity' => 1,
                                ],
                            ],
                        ], $delivery
                    );

                    $this->pushQuery('http://api.enter.ru/v2/payment-method/get-group?geo_id=14974&is_credit=1&client_id=site',
                        [
                            'product_list' => [
                                [
                                    'id'       => $product['result'][0]['id'],
                                    'quantity' => 1,
                                ],
                            ],
                        ], $paymentMethod
                    );
                }
            );

            // регион
            $this->pushQuery('http://api.enter.ru/v2/geo/get?id[0]=14974&client_id=site-web', [], $region
            );

            // дерево категорий
            $this->pushQuery('http://scms.enter.ru/api/category/tree?depth=1&load_medias=1', [], $categoryTree
            );

            // меню
            $this->pushQuery('http://scms.enter.ru/seo/main-menu?tags[0]=site-web', [], $menu
            );

            // выполнение запросов
            $curl->execute();

            //die(var_dump($query));

            /*
            var_dump($redirect);
            var_dump($abTest);
            var_dump($region);
            var_dump($product);
            var_dump($categoryTree);
            var_dump($delivery);
            var_dump($paymentMethod);
            var_dump($menu);
            */

            die(microtime(true) - $startAt);
        }

        protected function pushQuery($url, $data = [], &$result, &$error = null, $callback = null) {
            $query = $this->getCurl()->createQuery();

            $startingResponse = false;
            $query->request->options = [
                CURLOPT_HEADER         => false,
                CURLOPT_HEADERFUNCTION => function ($ch, $h) use (&$query, &$startingResponse) {
                    $value = trim($h);
                    if ($value === '') {
                        $startingResponse = true;
                    } elseif ($startingResponse) {
                        $startingResponse = false;
                        $query->response->headers = [$value];
                    } else {
                        $query->response->headers[] = $value;
                    }

                    return strlen($h);
                },
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_NOSIGNAL       => true,
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
                CURLOPT_ENCODING       => 'gzip,deflate',
                CURLOPT_URL            => $url,
                CURLOPT_TIMEOUT_MS     => 400,
            ];
            if ($data) {
                $query->request->options += [
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_POST       => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                ];
            }
            $query->resolveCallback = function () use (&$query, &$result, &$callback) {
                if ($query->response->error instanceof \EnterLab\Curl\Exception\ConnectException) {
                    var_dump(sprintf('Timeout reached for %s', $query->request->options[CURLOPT_URL]));
                }

                $result = json_decode($query->response->body, true);

                if (is_callable($callback)) {
                    call_user_func($callback);
                }
            };

            $this->getCurl()->addQuery($query);

            return $query;
        }
    }
}

namespace EnterApplication\Action\ProductCard\Get {
    class Request
    {
        /** @var string */
        public $urlPath;
        /** @var array */
        public $productCriteria;
        /** @var string */
        public $regionId;
    }
}