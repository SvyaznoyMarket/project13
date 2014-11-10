<?php

namespace Controller\Product;

class BasicRecommendedAction {

    //use _DebugTestTrait; // for debug

    protected $retailrocketMethodName;
    protected $smartengineMethodName;
    protected $actionType;
    protected $actionTitle;
    protected $name;
    protected $engine;

    static public $recomendedPartners = [
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

        return new \Http\JsonResponse($this->getResponseData($productId, $request));
    }


    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return array
     * @throws \Exception\NotFoundException
     */
    public function getResponseData($productId, \Http\Request $request) {
        $responseData = [];

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $key = \App::abTest()->getTest('other')->getChosenCase()->getKey();

            \App::logger()->info(sprintf('abTest.key=%s, response.cookie.switch=%s', $key, $request->cookies->get(\App::config()->abTest['cookieName'])));

            /*if ('retailrocket' == $key) {
                $products = $this->getProductsFromRetailrocket($product, $request, $this->retailrocketMethodName);
            } elseif ('hybrid' == $key) {
                $products = $this->getProductsHybrid($product, $request, $this->retailrocketMethodName);
            } else {
                $products = $this->getProductsFromSmartengine($product, $request, $this->smartengineMethodName);
            }*/
            /*
             * UPD: Отключаем Smartengine СОВСЕМ
            */
            $products = $this->getProductsFromRetailrocket($product, $request, $this->retailrocketMethodName); // UPD

            if ( !is_array($products) ) {
                throw new \Exception(sprintf('Not found products data in response. ActionType: %s', $this->actionType));
            }

            $responseData = [
                'success' => true,
                'content' => \App::closureTemplating()->render('product/__slider', [
                    'title' => $this->actionTitle,
                    'products' => $products,
                    'isRetailrocketRecommendation' => true,
                    'retailrocketMethod' => $this->retailrocketMethodName,
                ]),
            ];


        } catch (\Exception $e) {
            \App::logger()->error($e, [$this->actionType]);

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return $responseData;
    }


    /**
     * @param   array()                         $ids
     * @param   string                          $senderName
     * @return  \Model\Product\Entity[]         $products
     * @throws  \Exception
     */
    protected function getProducts($ids, $senderName) {
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

            // Сссылки переделываем перед формированием данных для модели товара
            // через метод \Controller\Product\BasicRecommendedAction::prepareLink()
            /*$link = $product->getLink();
            $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'sender=' . $senderName . '|' . $product->getId();

            if ('upsale' == $this->name) {
                $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'from=cart_rec';
                $product->setIsUpsale(true);
            }

            $product->setLink($link);*/

            if ('upsale' == $this->name) {
                $product->setIsUpsale(true);
            }

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
     * !!! Не используется теперь
     *
     * @param \Model\Product\Entity     $product
     * @param \Http\Request             $request
     * @param string                    $method
     * @return \Model\Product\Entity[]  $products
     * @throws \Exception
     *
    protected function getProductsFromSmartengine($product, \Http\Request $request, $method = 'relateditems')
    {
        \App::logger()->debug('Exec ' . __METHOD__);
        $this->setEngine('smartengine');

        //print '** This is Smartengine Method. Should be disabled **'; // tmp, for debug

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

        $products = $this->getProducts($ids, $client::NAME);

        return $products;

    }*/


    /**
     * @param \Model\Product\Entity     $product
     * @param \Http\Request             $request
     * @param string                    $method
     * @return \Model\Product\Entity[]  $products
     * @throws \Exception\
     */
    public function getProductsIdsFromRetailrocket( $product = null, \Http\Request $request, $method = 'UpSellItemToItems' ) {
        \App::logger()->debug('Exec ' . __METHOD__);
        $this->setEngine('retailrocket');

        $client = \App::retailrocketClient();
        $productId = $product ? $product->getId() : null;
        $ids = $client->query('Recomendation/' . $method, $productId);

        return $ids;
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
        $this->setEngine('retailrocket');

        $client = \App::retailrocketClient();
        $ids = $this->getProductsIdsFromRetailrocket( $product, $request, $method);
        $products = $this->getProducts($ids, $client::NAME);

        return $products;
    }



    /**
     * !!! Не используется теперь
     *
     * @param \Model\Product\Entity     $product
     * @param \Http\Request             $request
     * @param string                    $method
     * @return \Model\Product\Entity[]  $products
     *
    private function getProductsHybrid( $product, \Http\Request $request, $method = 'UpSellItemToItems' ) {

        if ( $this->actionType == 'AlsoViewedAction' ) { // if AlsoViewedAction

            // С этим товаром также смотрят - ВСЕГДА от RetailRocket,
            $products = $this->getProductsFromRetailrocket($product, $request, 'UpSellItemToItems');

        } else { // if SimilarAction

            // Похожие товары - ВСЕГДА  от SmartEngine
            $products = $this->getProductsFromSmartengine($product, $request, 'relateditems');

        }

        return $products;

    }*/

    /**
     * @return string
     */
    public function getRetailrocketMethodName()
    {
        $this->setEngine('retailrocket');
        return $this->retailrocketMethodName;
    }

    /**
     * @return mixed
     */
    public function getActionTitle()
    {
        return $this->actionTitle;
    }

    /**
     * @return mixed
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $link
     * @param $id
     * @param $senderName
     * @return string
     */
    static public function prepareLink(&$link, array $params) {
        $id = $params['id'];
        $senderName = $params['engine'];
        $recommName = $params['name'];

        $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'sender=' . $senderName . '|' . $id;

        if ('retailrocket' === $senderName) {
            //$link = $link . (false === strpos($link, '?') ? '?' : '&') . 'from=cart_rec';
            $link = $link . '&from=cart_rec';

            if (!empty($params['method'])) {
                $link = $link . '&rrMethod=' . $params['method'];
            }
        }

        return $link;
    }


    /**
     * @return string
     */
    public function getEngine() {
        return $this->engine;
    }

    /**
     * @param $eng
     * @return string
     */
    public function setEngine($eng) {
        return $this->engine = (string) $eng;
    }
}
