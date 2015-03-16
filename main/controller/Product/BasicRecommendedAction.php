<?php

namespace Controller\Product;

class BasicRecommendedAction {

    //use _DebugTestTrait; // for debug

    protected $retailrocketMethodName;
    protected $actionType;
    protected $actionTitle;
    protected $name;
    protected $engine;

    static public $recomendedPartners = [
        \RetailRocket\Client::NAME,
    ];


    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute($productId, \Http\Request $request) {
        $responseData = [];

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $products = $this->getProductsFromRetailrocket($product, $request, $this->retailrocketMethodName); // UPD

            if ( !is_array($products) ) {
                throw new \Exception(sprintf('Not found products data in response. ActionType: %s', $this->actionType));
            }

            $responseData = [
                'success' => true,
                'content' => \App::closureTemplating()->render('product/__slider', [
                    'title'                        => $this->actionTitle,
                    'products'                     => $products,
                    'count'                        => count($products),
                    'isRetailrocketRecommendation' => true,
                    'retailrocketMethod'           => $this->retailrocketMethodName,
                ]),
            ];


        } catch (\Exception $e) {
            \App::logger()->error($e, [$this->actionType]);

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return new \Http\JsonResponse($responseData);
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

    /** @deprecated
     * @param $link
     * @param $params
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
