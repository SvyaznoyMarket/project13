<?php

namespace Controller\Product;

class SimilarAction {


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

                if ( 'smartengine' == $key ) {
                    $products = $this->smartengineClient($product, $request);
                } elseif ( 'retailrocket' == $key ) {
                    $products = $this->retailrocketClient($product, $request);
                } elseif ( 'retailrocket/ItemToItems' == $key) {
                    $products = $this->retailrocketClient($product, $request, 'ItemToItems');
                }

                if ( !isset($products) || !is_array($products) ) {
                    return new \Http\JsonResponse([
                        'success' => false,
                        'error' => ['code' => '404', 'message' => 'Not found products data in response'],
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
    public function smartengineClient($product, \Http\Request $request)
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

            $products = $this->getProducts($ids);

            return $products;

        } catch (\Exception $e) {
            \App::logger()->error($e, ['smartengine']);
            return $this->error($e);
        }
    }



    /**
     * @param \Model\Product\Entity     $product
     * @param \Http\Request             $request
     * @param string                    $method
     * @return \Http\JsonResponse|\Model\Product\Entity[]
     */
    public function retailrocketClient( $product, \Http\Request $request, $method = 'UpSellItemToItems' ) {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            $client = \App::retailrocketClient();

            $productId = $product->getId();

            $ids = $client->query('Recomendation/' . $method, $productId);

            //if (!count($ids)) throw new \Exception(sprintf('Не получено товаров методом %1$s для товара #%2$s', $method, $productId));

            $products = $this->getProducts($ids);

            return $products;

            /*
            $return = [];
            foreach ($products as $i => $product) {
                if (!$product->getIsBuyable()) continue;

                $return[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'image' => $product->getImageUrl(),
                    'rating' => $product->getRating(),
                    'link' => $product->getLink() . (false === strpos($product->getLink(), '?') ? '?' : '&') . 'sender=' . \RetailRocket\Client::NAME . '|' . $product->getId(),
                    'price' => $product->getPrice(),
                    'data' => \Kissmetrics\Manager::getProductEvent($product, $i + 1, 'Similar'),
                ];
            }

            if (!count($return)) throw new \Exception(sprintf('Нечего возвращать. Метод %1$s для товара #%2$s', $method, $productId));

            return new \Http\JsonResponse($return);
            */

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
     * @return  \Model\Product\Entity[]         $products
     * @throws  \Exception
     */
    private function getProducts($ids) {
        if (!(bool)$ids) {
            throw new \Exception('Рекомендации не получены');
        }

        $products = \RepositoryManager::product()->getCollectionById($ids);
        foreach ($products as $i => $product) {
            if (!$product->getIsBuyable()) {
                unset($products[$i]);
            }
        }

        if (!(bool)$products) {
            throw new \Exception('Нет товаров');
        }

        return $products;
    }


}