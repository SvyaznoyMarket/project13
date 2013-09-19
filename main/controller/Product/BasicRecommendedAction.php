<?php

namespace Controller\Product;

class BasicRecommendedAction {

    //use _DebugTestTrait; // for debug

    protected $retailrocketMethodName;
    protected $smartengineMethodName;
    protected $actionType;
    protected $actionTitle;

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
        \App::logger()->debug('Exec ' . __METHOD__);

        $responseData = [];

        try {
            if (\App::config()->crossss['enabled']) {
                (new \Controller\Crossss\ProductAction())->recommended($request, $productId);
            }

            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $key = \App::abTest()->getCase()->getKey();

            \App::logger()->info(sprintf('abTest.key=%s, response.cookie.switch=%s', $key, $request->cookies->get('switch')));

            $categoryBranch = $product->getCategory();
            $rootCategory = $categoryBranch[1];

            if(!empty($categoryBranch[1]) && 
                in_array($categoryBranch[1]->getToken(), ['muzikalnie-instrumenti-2422', 'muzikalnie-instrumenti-2396']) &&
                (new \DateTime('now')) < (new \DateTime('2013-10-11'))) {
                $products = $this->getProductsFromSmartengine($product, $request, $this->smartengineMethodName);
            } else {
                $products = $this->getProductsFromRetailrocket($product, $request, $this->retailrocketMethodName);
            }

            if ( !is_array($products) ) {
                throw new \Exception(sprintf('Not found products data in response. ActionType: %s', $this->actionType));
            }

            $responseData = [
                'success' => true,
                'content' => \App::closureTemplating()->render('product/__slider', [
                    'title' => $this->actionTitle,
                    'products' => $products,
                ]),
            ];


        } catch (\Exception $e) {
            \App::logger()->error($e, [$this->actionType]);
            return new \Http\JsonResponse([
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ]);
        }

        return new \Http\JsonResponse($responseData);

    }





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
     * @param \Model\Product\Entity     $product
     * @param \Http\Request             $request
     * @param string                    $method
     * @return \Model\Product\Entity[]  $products
     * @throws \Exception
     */
    protected function getProductsFromSmartengine($product, \Http\Request $request, $method = 'relateditems')
    {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::smartengineClient();
        $user = \App::user()->getEntity();

        $params = [
            'sessionid' => session_id(),
            'itemid' => $product->getId(),
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
            throw new \Exception($r['error']['@message'] . ': ' . json_encode($r, JSON_UNESCAPED_UNICODE), (int)$r['error']['@code']);
        }


        $ids = (is_array($r['recommendeditems']) && array_key_exists('id', $r['recommendeditems']['item']))
            ? [$r['recommendeditems']['item']['id']]
            : array_map(function ($item) {
                return $item['id'];
            }, isset($r['recommendeditems']['item']) ? $r['recommendeditems']['item'] : []);

        $products = $this->prepareProducts($ids, $client::NAME);

        return $products;

    }




    /**
     * @param \Model\Product\Entity     $product
     * @param \Http\Request             $request
     * @param string                    $method
     * @return \Model\Product\Entity[]  $products
     * @throws \Exception\
     */
    protected function getProductsFromRetailrocket( $product, \Http\Request $request, $method = 'UpSellItemToItems' ) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::retailrocketClient();

        $productId = $product->getId();

        $ids = $client->query('Recomendation/' . $method, $productId);

        $products = $this->prepareProducts($ids, $client::NAME);

        return $products;

    }



    /**
     * @param \Model\Product\Entity     $product
     * @param \Http\Request             $request
     * @param string                    $method
     * @return \Model\Product\Entity[]  $products
     */
    private function getProductsHybrid( $product, \Http\Request $request, $method = 'UpSellItemToItems' ) {

        if ( $this->actionType == 'AlsoViewedAction' ) { // if AlsoViewedAction

            // С этим товаром также смотрят - ВСЕГДА от RetailRocket,
            $products = $this->getProductsFromRetailrocket($product, $request, 'UpSellItemToItems');

        } else { // if SimilarAction

            // Похожие товары - ВСЕГДА  от SmartEngine
            $products = $this->getProductsFromSmartengine($product, $request, 'relateditems');

        }

        return $products;

    }


}
