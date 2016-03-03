<?php

namespace Controller\Compare;

use EnterQuery as Query;
use Session\AbTest\AbTest;

class CompareAction {
    use \EnterApplication\CurlTrait;

    /** @var array */
    public $data;
    private $session;
    private $compareSessionKey;

    public function __construct() {
        $this->session = \App::session();
        $this->compareSessionKey = \App::config()->session['compareKey'];
        $this->data = (bool)$this->session->get($this->compareSessionKey) ? $this->session->get($this->compareSessionKey) : [];
    }

    public function execute(\Http\Request $request) {
        $client = \App::coreClientV2();

        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        $compareProducts = $this->session->get($this->compareSessionKey);
        $lastProduct = null;
        if ($compareProducts && is_array($compareProducts)) {
            $reviewsData = null;
            $productIds = array_keys($compareProducts);
            $lastProduct = end($compareProducts);

            foreach ($productIds as $productId) {
                $productsById[$productId] = new \Model\Product\Entity(['id' => $productId]);
            }

            \RepositoryManager::product()->prepareProductQueries($productsById, 'media property category label');
            \RepositoryManager::review()->prepareScoreCollection($productsById, function($data) use(&$reviewsData){
                $reviewsData = $data;
            });
            $client->execute();

            $compareGroups = $this->getCompareGroups($compareProducts, $productsById, $reviewsData);
        } else {
            $compareGroups = [];
        }

        /** @var \Model\Config\Entity[] $configParameters */
        $configParameters = [];
        $callbackPhrases = [];
        \RepositoryManager::config()->prepare(['site_call_phrases'], $configParameters, function(\Model\Config\Entity $entity) use (&$category, &$callbackPhrases) {
            if ('site_call_phrases' === $entity->name) {
                $callbackPhrases = !empty($entity->value['compare']) ? $entity->value['compare'] : [];
            }

            return true;
        });

        $client->execute();

        $page = new \View\Compare\CompareLayout();
        $page->setParam('products', $productsById);
        $page->setParam('compareGroups', $compareGroups);
        $page->setParam('activeCompareGroupIndex', $this->getActiveCompareGroupIndex($compareGroups, $request->get('typeId') !== null ? $request->get('typeId') : (isset($lastProduct['typeId']) ? $lastProduct['typeId'] : null)));
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($page->show());
    }

    private function getCompareGroups(array $compareProducts, $products, $reviewsData) {
        $compareGroups = [];

        $reviews = [];
        if (isset($reviewsData['product_scores']) && is_array($reviewsData['product_scores'])) {
            foreach ($reviewsData['product_scores'] as $item) {
                $reviews[$item['product_id']] = $item;
            }
        }

        if (is_array($products)) {

            foreach ($compareProducts as $compareProduct) {
                /** @var \Model\Product\Entity $product */
                $product = isset($products[$compareProduct['id']]) ? $products[$compareProduct['id']] : null;
                if (!$product) continue;

                $typeId = $product->getType() ? $product->getType()->getId() : null;
                if (!isset($compareGroups[$typeId])) {
                    $compareGroups[$typeId] = [
                        'type' => [
                            'id' => $typeId,
                            'name' => $product->getType() ? $product->getType()->getName() : null,
                        ],
                        'products' => [],
                        'propertyGroups' => [],
                    ];
                }

                $compareGroups[$typeId]['products'][] = $product;
            }

            foreach ($compareGroups as $key => $compareGroup) {
                $compareGroups[$key]['propertyGroups'] = $this->getPropertyGroups($compareGroup['products']);
            }

            $templateHelper = new \Helper\TemplateHelper();
            foreach ($compareGroups as $key => $compareGroup) {
                foreach ($compareGroup['products'] as $key2 => $product) {
                    $starCount = isset($reviews[$product->getId()]['star_score']) ? $reviews[$product->getId()]['star_score'] : 0;
                    $slotPartnerOffer = $product->getSlotPartnerOffer();

                    $compareGroups[$key]['products'][$key2] = [
                        'id' => $product->getId(),
                        'ui' => $product->getUi(),
                        'article' => $product->getArticle(),
                        'prefix' => $product->getPrefix(),
                        'webName' => $product->getWebName(),
                        'link' => $product->getLink() . '?' . http_build_query([
                            'sender' => [
                                'name'      => 'Enter',
                                'from'      => 'ComparePage'
                            ]
                        ]),
                        'price' => $templateHelper->formatPrice($product->getPrice()),
                        'priceOld' => $templateHelper->formatPrice($product->getPriceOld()),
                        'inShopStockOnly' => $product->isInShopStockOnly(),
                        'inShopShowroomOnly' => $product->isInShopShowroomOnly(),
                        'isBuyable' => $product->getIsBuyable(),
                        'statusId' => $product->getStatusId(),
                        'imageUrl' => $product->getMainImageUrl('product_120'),
                        'partnerName' => $slotPartnerOffer ? $slotPartnerOffer['name'] : '',
                        'partnerOfferUrl' => $slotPartnerOffer ? $slotPartnerOffer['offer'] : '',
                        'isSlot' => (bool)$slotPartnerOffer,
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
                        ]),
                        'sender' => json_encode([
                            'sender' => [
                                'name'      => 'Enter',
                                'position'  => 'ComparePage'
                            ]
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
                $groupName = trim($group->getName());
                if (!isset($propertyGroups[$groupName])) {
                    $propertyGroups[$groupName] = [
                        'name' => $groupName,
                        'properties' => [],
                    ];
                }

                foreach ($propertyGroup['properties'] as $property) {
                    /** @var \Model\Product\Property\Entity $property */
                    $propertyName = trim($property->getName());
                    if (!isset($propertyGroups[$groupName]['properties'][$propertyName])) {
                        $propertyGroups[$groupName]['properties'][$propertyName] = [
                            'name' => $propertyName,
                            'values' => $previousValuesStub,
                        ];
                    }

                    $propertyGroups[$groupName]['properties'][$propertyName]['values'][] = [
                        'text' => trim($property->getStringValue()),
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

    private function getActiveCompareGroupIndex(array $compareGroups, $typeId) {
        $typeId = (string)$typeId;

        foreach ($compareGroups as $i => $compareGroup) {
            if ((string)$compareGroup['type']['id'] === $typeId) {
                return $i;
            }
        }

        return 0;
    }

    public function add(\Http\Request $request, $productId) {
        /** @var \Model\Product\Entity[] $products */
        $products = [new \Model\Product\Entity(['id' => $productId])];
        \RepositoryManager::product()->prepareProductQueries($products, 'media');
        \App::coreClientV2()->execute();

        if ($products) {
            $product = $products[0];

            if (!array_key_exists($product->getId(), $this->data)) {
                $this->data[$product->getId()] = [
                    'id'     => $product->getId(),
                    'ui'     => $product->getUi(),
                    'typeId' => $product->getType() ? $product->getType()->getId() : null,
                    'location' => $request->query->get('location'),
                ];
                $this->session->set($this->compareSessionKey, $this->data);
            }

            if ($request->isXmlHttpRequest()) {
                return new \Http\JsonResponse([
                    'compare' => $this->session->get($this->compareSessionKey),
                    'product' => [
                        'prefix' => $product->getPrefix(),
                        'webName' => $product->getWebName(),
                        'imageUrl' => $product->getMainImageUrl('product_60'),
                    ],
                ]);
            }
        }

        $returnUrl = $request->server->get('HTTP_REFERER');
        if (!$returnUrl) {
            $returnUrl = \App::router()->generate('homepage');
        }

        return new \Http\RedirectResponse($returnUrl);
    }

    public function delete(\Http\Request $request, $productId) {
        if (array_key_exists($productId, $this->data)) {
            unset($this->data[$productId]);
            $this->session->set($this->compareSessionKey, $this->data);
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse(['compare' => $this->session->get($this->compareSessionKey)]);
        }

        $returnUrl = $request->server->get('HTTP_REFERER') ?: \App::router()->generate('homepage');

        return new \Http\RedirectResponse($returnUrl);
    }

    public function clear() {
        $this->session->set($this->compareSessionKey, []);
    }
} 