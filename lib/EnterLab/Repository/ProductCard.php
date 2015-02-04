<?php

namespace EnterLab\Repository;

class ProductCard
{
    use \EnterLab\Application\CurlTrait;

    public function get()
    {
        $startAt = microtime(true);

        $curl = $this->getCurl();

        // редирект
        $this->pushQuery(
            'http://scms.enter.ru/seo/redirect?from_url=/product/children/pedalnaya-mashina-bugati-disney-tachki-2010108013848',
            [],
            $redirect
        );

        // аб-тест
        $this->pushQuery(
            'http://scms.enter.ru/api/ab_test/get-active',
            [],
            $abTest
        );

        // товар
        $delivery = null;
        $paymentMethod = null;
        $this->pushQuery(
            'http://api.enter.ru/v2/product/get?select_type=slug&slug=pedalnaya-mashina-bugati-disney-tachki-2010108013848&geo_id=14974&client_id=site',
            [],
            $product,
            $productError,
            function() use (&$product, &$delivery, &$paymentMethod) {
                $this->pushQuery(
                    'http://api.enter.ru/v2/delivery/calc2?geo_id=14974&client_id=site',
                    [
                        'product_list' => [
                            [
                                'id'       => $product['result'][0]['id'],
                                'quantity' => 1,
                            ],
                        ],
                    ],
                    $delivery
                );

                $this->pushQuery(
                    'http://api.enter.ru/v2/payment-method/get-group?geo_id=14974&is_credit=1&client_id=site',
                    [
                        'product_list' => [
                            [
                                'id'       => $product['result'][0]['id'],
                                'quantity' => 1,
                            ],
                        ],
                    ],
                    $paymentMethod
                );
            }
        );

        // регион
        $this->pushQuery(
            'http://api.enter.ru/v2/geo/get?id[0]=14974&client_id=site-web',
            [],
            $region
        );

        // дерево категорий
        $this->pushQuery(
            'http://scms.enter.ru/api/category/tree?depth=1&load_medias=1',
            [],
            $categoryTree
        );

        // меню
        $this->pushQuery(
            'http://scms.enter.ru/seo/main-menu?tags[0]=site-web',
            [],
            $menu
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

    protected function pushQuery($url, $data = [], &$result, &$error = null, $callback = null)
    {
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
        $query->resolveCallback = function() use (&$query, &$result, &$callback) {
            if ($query->response->error instanceof \EnterLab\Curl\Exception\ConnectException) {
                var_dump('Timeout reached');
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