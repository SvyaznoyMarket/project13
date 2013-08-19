<?php

namespace Controller\Product;

class SimilarAction {

    static public $recomendedPartners = [
        \Smartengine\Client::NAME,
        \RetailRocket\Client::NAME,
    ];


    /**
     * Разводящий метод: запускает либо smartengineClient , либо retailrocketClient
     *
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute($productId, \Http\Request $request)
    {

        try {

            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $ABtestOption = \App::abTest()->getOption('test');
            $ABtest = reset($ABtestOption); /* @var $ABtest \Model\Abtest\Entity */


            if ( !empty( $ABtest ) ) { /// if

                $title = $ABtest->getName();
                $key = $ABtest->getKey();

                if ('smartengine' == $key) {
                    $products = $this->getProductsFromSmartengine($product, $request);
                } elseif ('retailrocketItemToItems' == $key) {
                    $products = $this->getProductsFromRetailrocket($product, $request, 'ItemToItems');
                } elseif ('retailrocket' == $key || 'retailrocketUpSellItemToItems' == $key) {
                    $products = $this->getProductsFromRetailrocket($product, $request, 'UpSellItemToItems');
                }

                if ( !isset($products) || !is_array($products) ) {
                    return new \Http\JsonResponse([
                        'success' => false,
                        'error' => ['code' => '404', 'message' => 'Not found products data in response. Method: ' . $key],
                    ]);
                }


                return new \Http\JsonResponse([
                    'success' => true,
                    'content' => \App::closureTemplating()->render('product/__slider', [
                        'title' => $title,
                        'products' => $products,
                    ]),
                ]);

            }/// if

            return new \Http\JsonResponse([
                'success' => false,
                'error' => ['code' => '404', 'message' => 'Not found data in config'],
            ]);

        } catch (\Exception $e) {
            \App::logger()->error($e, ['SimilarAction']);
            return $this->error($e);
        }

    }



    /**
     * @param \Model\Product\Entity         $product
     * @param \Http\Request                 $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function getProductsFromSmartengine($product, \Http\Request $request)
    {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            $client = \App::smartengineClient();
            $user = \App::user()->getEntity();

            $params = [
                'sessionid'       => session_id(),
                'itemid'          => $product->getId(),
                'assoctype'       => 'IS_SIMILAR',
                'numberOfResults' => 15,
            ];
            if ($user) {
                $params['userid'] = $user->getId();
            }
            $params['itemtype'] = $product->getMainCategory() ? $product->getMainCategory()->getId() : null;
            $r = $client->query('relateditems', $params);

            if (isset($r['error'])) {
                throw new \Exception($r['error']['@message'] . ': '. json_encode($r, JSON_UNESCAPED_UNICODE), (int)$r['error']['@code']);
            }

            $ids = (is_array($r['recommendeditems']) && array_key_exists('id', $r['recommendeditems']['item']))
                ? [$r['recommendeditems']['item']['id']]
                : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : []);

            $products = $this->prepareProducts($ids, $client::NAME);

            return $products;

        } catch (\Exception $e) {
            \App::logger()->error($e, ['SmartEngine']);
            return $this->error($e);
        }
    }



    /**
     * @param \Model\Product\Entity     $product
     * @param \Http\Request             $request
     * @param string                    $method
     * @return \Http\JsonResponse|\Model\Product\Entity[]
     * @throws \Exception\NotFoundException
     */
    public function getProductsFromRetailrocket( $product, \Http\Request $request, $method = 'UpSellItemToItems' ) {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            $client = \App::retailrocketClient();

            $productId = $product->getId();

            $ids = $client->query('Recomendation/' . $method, $productId);

            $products = $this->prepareProducts($ids, $client::NAME);

            return $products;


        } catch (\Exception $e) {
            \App::logger()->error($e, ['RetailRocket']);
            return $this->error($e);
        }

    }



    /**
     * @param \Exception $e
     * @return \Http\JsonResponse
     */
    private function error(\Exception $e)
    {
        return new \Http\JsonResponse([
            'success' => false,
            'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
        ]);
    }



    /**
     * @param   array()                         $ids
     * @param   string                          $senderName
     * @return  \Model\Product\Entity[]         $products
     * @throws  \Exception
     */
    private function prepareProducts($ids, $senderName) {
        if (!(bool)$ids) {
            throw new \Exception('Рекомендации не получены');
        }

        $products = \RepositoryManager::product()->getCollectionById($ids);

        foreach ($products as $i => $product) {
            /* @var product Model\Product\Entity */

            //if (!$product->getIsBuyable()) continue;
            if (!$product->getIsBuyable())  {
                unset($products[$i]);
                continue;
            }

            $link = $product->getLink();
            $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'sender=' . $senderName . '|' . $product->getId();
            $product->setLink($link);

            /*
            $return[] = [
                'id'     => $product->getId(),
                'name'   => $product->getName(),
                'image'  => $product->getImageUrl(),
                'rating' => $product->getRating(),
                'link'   => $product->getLink() . (false === strpos($product->getLink(), '?') ? '?' : '&') . 'sender=' . $senderName . '|' . $product->getId(),
                'price'  => $product->getPrice(),
                'data'   => \Kissmetrics\Manager::getProductEvent($product, $i+1, 'Similar'),
            ];
            */
        }

        if (!(bool)$products) {
            throw new \Exception('Нет товаров');
        }

        return $products;
    }


}