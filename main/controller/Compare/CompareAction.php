<?php

namespace Controller\Compare;

class CompareAction {

    /** @var array */
    public $data;
    private $session;
    private $compareSessionKey;

    public function __construct() {
        $this->session = \App::session();
        $this->compareSessionKey = \App::config()->session['compareKey'];
        $this->data = (bool)$this->session->get($this->compareSessionKey) ? $this->session->get($this->compareSessionKey) : [];
    }

    public function execute() {
        $compareProducts = $this->session->get($this->compareSessionKey);
        if ($compareProducts && is_array($compareProducts)) {
            $productData = null;
            $reviewsData = null;
            $productIds = array_keys($compareProducts);
            
            $client = \App::coreClientV2();
            $client->addQuery(
                'product/get',
                [
                    'select_type' => 'id',
                    'id'          => $productIds,
                    'geo_id'      => \App::user()->getRegion()->getId(),
                ],
                [],
                function($data) use(&$productData) {
                    $productData = $data;
                }
            );
            
            \RepositoryManager::review()->prepareScoreCollection($productIds, function($data) use(&$reviewsData){
                $reviewsData = $data;
            });
            
            $client->execute();

            $compareGroups = $this->getCompareGroups($compareProducts, $productData, $reviewsData);
        } else {
            $compareGroups = [];
        }

        $page = new \View\Compare\CompareLayout();
        $page->setParam('compareGroups', $compareGroups);
        return new \Http\Response($page->show());
    }
    
    private function getCompareGroups(array $compareProducts, $productData, $reviewsData) {
        $compareGroups = [];
        
        $reviews = [];
        if (isset($reviewsData['product_scores']) && is_array($reviewsData['product_scores'])) {
            foreach ($reviewsData['product_scores'] as $item) {
                $reviews[$item['product_id']] = $item;
            }
        }
        
        if (is_array($productData)) {
            $products = [];
            foreach ($productData as $item) {
                $product = new \Model\Product\Entity($item);
                $products[$product->getId()] = $product;
            }
            
            foreach ($compareProducts as $compareProduct) {
                /** @var \Model\Product\Entity $product */
                $product = $products[$compareProduct['id']];

                if (!isset($compareGroups[$compareProduct['categoryId']])) {
                    $lastCategory = $product->getLastCategory();
                    $compareGroups[$compareProduct['categoryId']] = [
                        'category' => [
                            'id' => $lastCategory->getId(),
                            'name' => $lastCategory->getName(),
                        ],
                        'products' => [],
                        'propertyGroups' => [],
                    ];
                }

                $compareGroups[$compareProduct['categoryId']]['products'][] = $product;
            }

            foreach ($compareGroups as $key => $compareGroup) {
                $compareGroups[$key]['propertyGroups'] = $this->getPropertyGroups($compareGroup['products']);
            }

            $templateHelper = new \Helper\TemplateHelper();
            foreach ($compareGroups as $key => $compareGroup) {
                foreach ($compareGroup['products'] as $key2 => $product) {
                    $starCount = isset($reviews[$product->getId()]['star_score']) ? $reviews[$product->getId()]['star_score'] : 0;
                    
                    $compareGroups[$key]['products'][$key2] = [
                        'id' => $product->getId(),
                        'prefix' => $product->getPrefix(),
                        'webName' => $product->getWebName(),
                        'link' => $product->getLink(),
                        'price' => $templateHelper->formatPrice($product->getPrice()),
                        'priceOld' => $templateHelper->formatPrice($product->getPriceOld()),
                        'inShopOnly' => $product->isInShopOnly(),
                        'inShopShowroomOnly' => $product->isInShopShowroomOnly(),
                        'isBuyable' => $product->getIsBuyable(),
                        'statusId' => $product->getStatusId(),
                        'imageUrl' => $product->getImageUrl(1),
                        'reviews' => [
                            'stars' => [
                                'notEmpty' => array_pad([], $starCount, null),
                                'empty' => array_pad([], 5 - $starCount, null),
                            ],
                            'count' => isset($reviews[$product->getId()]['num_reviews']) ? $reviews[$product->getId()]['num_reviews'] : null,
                        ],
                        'deleteFromCompareUrl' => \App::router()->generate('compare.delete', ['productId' => $product->getId()]),
                        'upsale' => json_encode([
                            'url' => \App::router()->generate('product.upsale', ['productId' => $product->getId()]),
                            'fromUpsale' => ($templateHelper->hasParam('from') && 'cart_rec' === $templateHelper->getParam('from')) ? true : false,
                        ])
                    ];
                }
            }

            $compareGroups = array_values($compareGroups);
        }
        
        return $compareGroups;
    }

    /**
     * @param \Model\Product\Entity[] $products
     * @return array
     */
    private function getPropertyGroups($products) {
        $propertyGroups = [];
        $previousValuesStub = [];
        $productNumber = 0;
        foreach ($products as $product) {
            $productNumber++;

            foreach ($product->getGroupedProperties() as $propertyGroup) {
                /** @var \Model\Product\Property\Group\Entity $group */
                $group = $propertyGroup['group'];
                if (!isset($propertyGroups[$group->getId()])) {
                    $propertyGroups[$group->getId()] = [
                        'name' => $group->getName(),
                        'properties' => [],
                    ];
                }

                foreach ($propertyGroup['properties'] as $property) {
                    /** @var \Model\Product\Property\Entity $property */
                    if (!isset($propertyGroups[$group->getId()]['properties'][$property->getId()])) {
                        $propertyGroups[$group->getId()]['properties'][$property->getId()] = [
                            'name' => $property->getName(),
                            'values' => $previousValuesStub,
                        ];
                    }

                    $propertyGroups[$group->getId()]['properties'][$property->getId()]['values'][] = [
                        'text' => $property->getStringValue(),
                        'productId' => $product->getId(),
                    ];
                }
            }

            foreach ($propertyGroups as $key => $propertyGroup) {
                foreach ($propertyGroup['properties'] as $key2 => $property) {
                    if (count($property['values']) < $productNumber) {
                        $propertyGroups[$key]['properties'][$key2]['values'][] = [
                            'text' => '',
                            'productId' => $product->getId(),
                        ];
                    }
                }
            }

            $previousValuesStub[] = [
                'text' => '',
                'productId' => $product->getId(),
            ];
        }

        foreach ($propertyGroups as $key => $propertyGroup) {
            $propertyGroups[$key]['properties'] = array_values($propertyGroup['properties']);

            foreach ($propertyGroups[$key]['properties'] as $key2 => $property) {
                $propertyGroups[$key]['properties'][$key2]['values'] = array_values($property['values']);
            }

            if (!count($propertyGroups[$key]['properties'])) {
                unset($propertyGroups[$key]);
            }
        }

        $propertyGroups = array_values($propertyGroups);

        return $propertyGroups;
    }

    public function add(\Http\Request $request, $productId) {

        $product = \RepositoryManager::product()->getEntityById($productId);

        if (!array_key_exists($product->getId(), $this->data)) {
            $this->data[$product->getId()] = [
                'id'            => $product->getId(),
                'ui'            => $product->getUi(),
                'categoryId'    => $product->getLastCategory()->getId()
            ];
            $this->session->set($this->compareSessionKey, $this->data);
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse(['compare' => $this->session->get($this->compareSessionKey)]);
        }

        $referrer = $request->server->get('HTTP_REFERER');
        return new \Http\RedirectResponse($referrer);
    }

    public function delete(\Http\Request $request, $productId) {
        if (array_key_exists($productId, $this->data)) {
            unset($this->data[$productId]);
            $this->session->set($this->compareSessionKey, $this->data);
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse(['compare' => $this->session->get($this->compareSessionKey)]);
        }

        return new \Http\RedirectResponse($request->server->get('HTTP_REFERER'));
    }

    public function clear() {
        $this->session->set($this->compareSessionKey, []);
    }
} 