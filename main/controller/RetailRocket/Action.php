<?php

namespace Controller\RetailRocket;


class Action
{
    private $RetailRocket;

    public function __construct($config = []) {
        $this->RetailRocket = new \RetailRocket\Client( $config, \App::logger() );
    }


    public function execute(\Http\Request $request, $productId)
    {
        /*$RR = &$this->RetailRocket;
        $resp = $RR->query('Recomendation/UpSellItemToItems', 1757);
        print_r($resp);

        $resp = $RR->query('Recomendation/ItemToItems', 1757);
        print_r($resp);*/


        $resp = $this->pullProductSimilar($request, 1757);

        print '<pre>se';
        print $resp;
        print '</pre>';


        $resp = $this->getRecomendationUpSell($request, 1757);

        print '<pre>rr';
        //print_r($resp);
        print ($resp);
        print '</pre>';

    }


    public function getRecomendationUpSell( \Http\Request $request, $item_id ) {
        $RR = &$this->RetailRocket;
        $ids = $RR->query('Recomendation/UpSellItemToItems', $item_id);
        $products = \RepositoryManager::product()->getCollectionById($ids);

        /*
        foreach ($products as $i => $product) {
            if (!$product->getIsBuyable()) unset($products[$i]);
        }

        if (!count($products)) {
            throw new \Exception();
        }

        $additionalData = [];
        foreach ($products as $i => $product) {
            $additionalData[$product->getId()] = \Kissmetrics\Manager::getProductEvent($product, $i+1, 'Also Viewed');
        }*/

        $return = [];
        foreach ($products as $i => $product) {
            if (!$product->getIsBuyable()) continue;

            $return[] = [
                'id'     => $product->getId(),
                'name'   => $product->getName(),
                'image'  => $product->getImageUrl(),
                'rating' => $product->getRating(),
                'link'   => $product->getLink().(false === strpos($product->getLink(), '?') ? '?' : '&') . 'sender='.\RetailRocket\Client::NAME.'|'.$product->getId(),
                'price'  => $product->getPrice(),
                'data'   => \Kissmetrics\Manager::getProductEvent($product, $i+1, 'Similar'),
            ];
        }
        if (!count($return)) {
            throw new \Exception();
        }

        return new \Http\JsonResponse($return);


        //return $products;
    }





    /**
     * @param int $productId
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function pullProductSimilar( \Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

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
            if (!(bool)$r['recommendeditems']) {
                throw new \Exception();
            }

            $ids =
                array_key_exists('id', $r['recommendeditems']['item'])
                ? [$r['recommendeditems']['item']['id']]
                : array_map(function($item) { return $item['id']; }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : []);
            if (!count($ids)) {
                throw new \Exception();
            }

            $products = \RepositoryManager::product()->getCollectionById($ids);

            $return = [];
            foreach ($products as $i => $product) {
                if (!$product->getIsBuyable()) continue;

                $return[] = [
                    'id'     => $product->getId(),
                    'name'   => $product->getName(),
                    'image'  => $product->getImageUrl(),
                    'rating' => $product->getRating(),
                    'link'   => $product->getLink().(false === strpos($product->getLink(), '?') ? '?' : '&') . 'sender='.\RetailRocket\Client::NAME.'|'.$product->getId(),
                    'price'  => $product->getPrice(),
                    'data'   => \Kissmetrics\Manager::getProductEvent($product, $i+1, 'Similar'),
                ];
            }
            if (!count($return)) {
                throw new \Exception();
            }

            return new \Http\JsonResponse($return);

        } catch (\Exception $e) {
            \App::logger()->error($e, ['RetailRocket']);

            return new \Http\JsonResponse();
        }
    }



}