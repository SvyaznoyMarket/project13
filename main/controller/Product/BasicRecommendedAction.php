<?php

namespace Controller\Product;

class BasicRecommendedAction {

    use _DebugTestTrait;

    static public $recomendedPartners = [
        \Smartengine\Client::NAME,
        \RetailRocket\Client::NAME,
    ];


    /**
     * @param   array()                         $ids
     * @param   string                          $senderName
     * @return  \Model\Product\Entity[]         $products
     * @throws  \Exception
     */
    protected function prepareProducts($ids, $senderName) {
        if (!(bool)$ids) {
            throw new \Exception('Рекомендации не получены');
        }

        $products = \RepositoryManager::product()->getCollectionById($ids);

        foreach ($products as $i => $product) {
            /* @var product Model\Product\Entity */

            if (!$product->getIsBuyable())  {
                unset($products[$i]);
                continue;
            }

            $link = $product->getLink();
            $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'sender=' . $senderName . '|' . $product->getId();
            $product->setLink($link);

            //$additionalData[$product->getId()] = \Kissmetrics\Manager::getProductEvent($product, $i+1, 'Also Viewed');

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



    /**
     * @param \Model\Product\Entity         $product
     * @param \Http\Request                 $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    protected function getProductsFromSmartengine($product, \Http\Request $request, $method = 'relateditems')
    {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            $client = \App::smartengineClient();
            $user = \App::user()->getEntity();


            $params = [
                'sessionid'       => session_id(),
                'itemid'          => $product->getId(),
            ];
            if ($method == 'relateditems') {
                $params['assoctype'] = 'IS_SIMILAR';
                $params['numberOfResults'] = 15;
            }
            if ($user) $params['userid'] = $user->getId();

            $params['itemtype'] = $product->getMainCategory() ? $product->getMainCategory()->getId() : null;

            if ($method == 'otherusersalsoviewed') {
                $params['requesteditemtype'] = $product->getMainCategory() ? $product->getMainCategory()->getId() : null;
            }


            $r = $client->query($method, $params);

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
    protected function getProductsFromRetailrocket( $product, \Http\Request $request, $method = 'UpSellItemToItems' ) {
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
    protected function error(\Exception $e)
    {
        return new \Http\JsonResponse([
            'success' => false,
            'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
        ]);
    }




}
