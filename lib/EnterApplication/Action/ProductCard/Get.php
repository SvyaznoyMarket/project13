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
            $productQuery->prepare($productError, function() use ( // TODO: сделать массив функций
                &$productQuery,
                &$deliveryError,
                &$paymentGroupError,
                &$ratingError
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

                // рейтинг товаров
                try {
                    $ratingQuery = null;

                    if ($accessoryIds = array_slice((array)$productQuery->response->product['accessories'], 0, \App::config()->product['itemsPerPage'])) {
                        $ratingQuery = new Query\Product\Review\GetScoreByProductIdList();
                        $ratingQuery->productIds = array_merge($ratingQuery->productIds, $accessoryIds);
                    }

                    if ($ratingQuery) {
                        $ratingQuery->prepare($ratingError);
                    }
                } catch (\Exception $e) {
                    $ratingError = $e;
                }
            });

            // отзывы о товаре
            $reviewQuery = null;
            if ($request->productCriteria['token']) {
                $reviewQuery = new Query\Product\Review\GetByProductToken($request->productCriteria['token'], 1, 7);
            }
            if ($reviewQuery) {
                $reviewQuery->prepare($reviewError);
            }

            // редирект
            $redirectQuery = (new Query\Redirect\GetByUrl($request->urlPath))->prepare($redirectError); // TODO: throw Exception

            // аб-тест
            $abTestQuery = (new Query\AbTest\GetActive())->prepare($abTestError);

            // регион
            $regionQuery = (new Query\Region\GetById($request->regionId))->prepare($abTestError);

            // дерево категорий для меню
            //$categoryTreeQuery = (new Query\Product\Category\GetTree(null, 3, null, null, true))->prepare($categoryTreeError);
            $categoryRootTreeQuery = (new Query\Product\Category\GetRootTree(3))->prepare($categoryRootTreeError);

            // главное меню
            $menuQuery = (new Query\MainMenu\GetByTagList(['site-web']))->prepare($menuError);

            // выполнение запросов
            $curl->execute();

            die(microtime(true) - $startAt);
        }

        /**
         * @return Request
         */
        public function createRequest()
        {
            return new Request();
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