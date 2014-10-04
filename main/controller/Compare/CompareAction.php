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
        if ($compareProducts) {
            $compareGroups = [];
            $client = \App::coreClientV2();
            $client->addQuery(
                'product/get',
                [
                    'select_type' => 'id',
                    'id'          => array_keys($compareProducts),
                    'geo_id'      => \App::user()->getRegion()->getId(),
                ],
                [],
                function($data) use(&$compareProducts, &$compareGroups) {
                    if (is_array($data)) {
                        $products = [];
                        foreach ($data as $item) {
                            $product = new \Model\Product\Entity($item);
                            $products[$product->getId()] = $product;
                        }

                        $templateHelper = new \Helper\TemplateHelper();
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

                        foreach ($compareGroups as $key => $compareGroup) {
                            foreach ($compareGroup['products'] as $key2 => $product) {
                                $compareGroups[$key]['products'][$key2] = [
                                    'id' => $product->getId(),
                                    'prefix' => $product->getPrefix(),
                                    'webName' => $product->getWebName(),
                                    'link' => $product->getLink(),
                                    'price' => $templateHelper->formatPrice($product->getPrice()),
                                    'priceOld' => $templateHelper->formatPrice($product->getPriceOld()),
                                    'imageUrl' => $product->getImageUrl(1),
                                    'removeFromCompareUrl' => \App::router()->generate('compare.delete', ['productId' => $product->getId()]),
                                ];
                            }
                        }

                        $compareGroups = array_values($compareGroups);
                    }
                }
            );

            $client->execute();

            $page = new \View\Compare\CompareLayout();
            $page->setParam('compareGroups', $compareGroups);
            $page->setParam('page', $page);
            return new \Http\Response($page->show());
        }

        return new \Http\Response();
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
                        'isSimilar' => false,
                        'properties' => [],
                    ];
                }

                foreach ($propertyGroup['properties'] as $property) {
                    /** @var \Model\Product\Property\Entity $property */
                    if (!isset($propertyGroups[$group->getId()]['properties'][$property->getId()])) {
                        $propertyGroups[$group->getId()]['properties'][$property->getId()] = [
                            'name' => $property->getName(),
                            'isSimilar' => false,
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

            $isGroupSimilar = true;
            foreach ($propertyGroups[$key]['properties'] as $key2 => $property) {
                $propertyGroups[$key]['properties'][$key2]['values'] = array_values($property['values']);
                $propertyGroups[$key]['properties'][$key2]['isSimilar'] = count(array_unique(array_map(function($item){ return $item['text']; }, $property['values']))) == 1;

                if (!$propertyGroups[$key]['properties'][$key2]['isSimilar']) {
                    $isGroupSimilar = false;
                }
            }

            if ($isGroupSimilar) {
                $propertyGroups[$key]['isSimilar'] = true;
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