<?php

namespace Controller\Gift\ProductCategory;

use Session\AbTest\ABHelperTrait;

class Action {
    use ABHelperTrait;
    
    protected $pageTitle;

    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function category(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем текущий регион, если есть кука региона
        $regionConfig = [];
        if ($user->getRegionId()) {
            \App::dataStoreClient()->addQuery("region/{$user->getRegionId()}.json", [], function($data) use (&$regionConfig) {
                if ($data) {
                    $regionConfig = $data;
                }
            });

            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ($data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        $regionEntity = \App::user()->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            if (array_key_exists('reserve_as_buy', $regionConfig)) {
                $regionEntity->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
            }
            \App::user()->setRegion($regionEntity);
        }

        // подготовка 2-го пакета запросов

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollection([], function($data) use (&$filters) {
            foreach ($data as $item) {
                $filters[] = new \Model\Product\Filter\Entity($item);
            }
        });

        $this->createTagFilterProperties($filters);

        $categoryProperty = new \Model\Product\Filter\Entity();
        $categoryProperty->setId('category');
        $categoryProperty->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);
        $categoryProperty->setIsMultiple(true);
        $filters[] = $categoryProperty;

        \RepositoryManager::menu()->prepareCollection(function ($data) use ($categoryProperty) {
            if (isset($data['item']) && is_array($data['item'])) {
                foreach ($data['item'] as $item) {
                    if ($item['source']['id']) {
                        $categoryProperty->addOption(new \Model\Product\Filter\Option\Entity(['id' => $item['source']['id'], 'name' => $item['name']]));
                    }
                }
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        $shop = null;
        try {
            if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        // фильтры
        $productFilter = $this->getFilter($filters, $request, $shop);

        $pageNum = (int)$request->get('page', 1);
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pageNum));
        }

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);

        $productPager = $this->getProductPager($productFilter, $productSorting, $pageNum);

        // проверка на максимально допустимый номер страницы
        if (1 != $productPager->getPage() && $productPager->getPage() - $productPager->getLastPage() > 0) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        $productVideosByProduct = $this->getProductVideosByProduct($productPager);

        $columnCount = 4;

        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            $data = [
                'list'           => (new \View\Product\ListAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager,
                    $productVideosByProduct,
                    [],
                    null,
                    true,
                    $columnCount,
                    'light_with_bottom_description'
                ),
                'selectedFilter' => [],
                'pagination'     => (new \View\PaginationAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productPager
                ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                    \App::closureTemplating()->getParam('helper'),
                    $productSorting
                ),
                'page'           => [
                    'title'      => ''
                ],
                'countProducts' => $productPager->count(),
            ];

            return new \Http\JsonResponse($data);
        }

        $page = new \View\Gift\ProductCategory\LeafPage();
        $page->setParam('productFilter', $productFilter);
        $page->setParam('productPager', $productPager);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productVideosByProduct', $productVideosByProduct);
        $page->setParam('columnCount', $columnCount);
        $page->setParam('isNewMainPage', $this->isNewMainPage());
        $page->setGlobalParam('shop', $shop);

        return new \Http\Response($page->show());
    }

    private function createTagFilterProperties(array &$filters) {
        foreach ($this->getTagFilterPropertyValues() as $id => $values) {
            $property = new \Model\Product\Filter\Entity();
            $property->setId($id);
            $property->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);
            $property->setIsMultiple(false);
            foreach ($values as $value) {
                $property->addOption(new \Model\Product\Filter\Option\Entity(['id' => $value['id'], 'name' => $value['name']]));
            }
            $filters[] = $property;
        }
    }
    
    private function getTagFilterPropertyValues() {
        return [
            'holiday' => [
                ['id' => 706, 'name' => 'Новый Год'],
                ['id' => 707, 'name' => 'День рождения'],
                ['id' => 708, 'name' => 'Юбилей'],
                ['id' => 709, 'name' => 'День свадьбы'],
                ['id' => 710, 'name' => 'Новоселье'],
                ['id' => 711, 'name' => 'Благодарность'],
                ['id' => 712, 'name' => 'Любой праздник'],
            ],
            'sex' => [
                ['id' => 687, 'name' => 'Женщине'],
                ['id' => 688, 'name' => 'Мужчине'],
            ],
            'status' => [
                ['id' => 689, 'name' => 'Любимой'],
                ['id' => 690, 'name' => 'Коллеге'],
                ['id' => 692, 'name' => 'Боссу'],
                ['id' => 693, 'name' => 'Подруге'],
                ['id' => 694, 'name' => 'Девочке'],
                ['id' => 695, 'name' => 'Маме'],
                ['id' => 696, 'name' => 'Бабушке'],
                ['id' => 697, 'name' => 'Себе'],
                
                ['id' => 698, 'name' => 'Любимому'],
                ['id' => 690, 'name' => 'Коллеге'],
                ['id' => 692, 'name' => 'Боссу'],
                ['id' => 699, 'name' => 'Другу'],
                ['id' => 700, 'name' => 'Мальчику'],
                ['id' => 703, 'name' => 'Папе'],
                ['id' => 705, 'name' => 'Дедушке'],
                ['id' => 697, 'name' => 'Себе'],
            ],
            'age' => [
                ['id' => 713, 'name' => '0–1 год'],
                ['id' => 716, 'name' => '1–3 года'],
                ['id' => 717, 'name' => '4–7 лет'],
                ['id' => 718, 'name' => '8–12 лет'],
                ['id' => 719, 'name' => '13–16 лет'],
                ['id' => 720, 'name' => '17–23 года'],
                ['id' => 721, 'name' => '24–35 лет'],
                ['id' => 722, 'name' => '36–60 лет'],
                ['id' => 723, 'name' => 'Старше 60'],
                ['id' => 724, 'name' => 'Возраст не имеет значения'],
            ],
        ];
    }
    
    private function hasTagFilterPropertyValue($propertyName, $valueId) {
        foreach ($this->getTagFilterPropertyValues()[$propertyName] as $value) {
            if ($value['id'] == $valueId) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * @param int $pageNum
     * @return \Iterator\EntityPager
     */
    private function getProductPager(\Model\Product\Filter $productFilter, \Model\Product\Sorting $productSorting, $pageNum) {
        $itemsPerPage = \App::config()->product['itemsPerPage'];
        $limit = $itemsPerPage;
        $offset = ($pageNum - 1) * $limit;

        $region = \App::user()->getRegion();

        $repository = \RepositoryManager::product();
        $repository->setEntityClass('\\Model\\Product\\Entity');
        
        $productIds = [];
        $productCount = 0;
        $repository->prepareIteratorByFilter(
            $this->getProductFilterDump($productFilter),
            $productSorting->dump(),
            $offset,
            $limit,
            $region,
            function($data) use (&$productIds, &$productCount) {
                if (isset($data['list'][0])) {
                    $productIds = $data['list'];
                }

                if (isset($data['count'])) {
                    $productCount = (int)$data['count'];
                }
            }
        );
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $products = [];
        if ($productIds) {
            $repository->prepareCollectionById($productIds, $region, function($data) use (&$products) {
                if (is_array($data)) {
                    foreach ($data as $item) {
                        $products[] = new \Model\Product\Entity($item);
                    }
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $scoreData = [];
        if ($products) {
            $productUIs = [];
            foreach ($products as $product) {
                if (!$product instanceof \Model\Product\BasicEntity) continue;
                $productUIs[] = $product->getUi();
            }

            \RepositoryManager::review()->prepareScoreCollectionByUi($productUIs, function($data) use (&$scoreData) {
                if (isset($data['product_scores'][0])) {
                    $scoreData = $data;
                }
            });
        }

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        \RepositoryManager::review()->addScores($products, $scoreData);

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($itemsPerPage);
        return $productPager;
    }
    
    private function getProductFilterDump(\Model\Product\Filter $productFilter) {
        $tagDump = ['tag_and', 1, []];
        $filterDump = $productFilter->dump();
        for ($i = count($filterDump) - 1; $i >= 0; $i--) {
            if (in_array($filterDump[$i][0], ['holiday', 'sex', 'status', 'age'], true)) {
                $tagDump[2] = array_merge($tagDump[2], $filterDump[$i][2]);
                unset($filterDump[$i]);
            }
        }
        
        $filterDump = array_values($filterDump);
        $filterDump[] = $tagDump;
        
        return $filterDump;
    }

    /**
     * @return array
     */
    private function getProductVideosByProduct(\Iterator\EntityPager $productPager) {
        $productVideosByProduct = [];
        foreach ($productPager as $product) {
            /** @var $product \Model\Product\Entity */
            $productVideosByProduct[$product->getId()] = [];
        }

        if ($productVideosByProduct) {
            \RepositoryManager::productVideo()->prepareCollectionByProductIds(array_keys($productVideosByProduct), function($data) use (&$productVideosByProduct) {
                if (is_array($data)) {
                    foreach ($data as $id => $items) {
                        if (!is_array($items)) {
                            continue;
                        }

                        foreach ($items as $item) {
                            $productVideosByProduct[$id][] = new \Model\Product\Video\Entity((array)$item);
                        }
                    }
                }
            });

            \App::dataStoreClient()->execute(\App::config()->dataStore['retryTimeout']['tiny'], \App::config()->dataStore['retryCount']);
        }

        return $productVideosByProduct;
    }

    /**
     * @param \Http\Request $request
     * @return array
     */
    private function getFilterFromUrl(\Http\Request $request) {
        // добывание фильтров из http-запроса
        if ('POST' == $request->getMethod()) {
            $requestData = clone $request->request;
        } else {
            $requestData = clone $request->query;
        }
        
        if (!$this->hasTagFilterPropertyValue('holiday', $requestData->get('f-holiday'))) {
            $requestData->set('f-holiday', 706);
        }
        
        if (!$this->hasTagFilterPropertyValue('sex', $requestData->get('f-sex'))) {
            $requestData->set('f-sex', 687);
        }
        
        if (!$this->hasTagFilterPropertyValue('status', $requestData->get('f-status'))) {
            if ($requestData->get('f-sex') == 687) {
                $requestData->set('f-status', 690);
            } else {
                $requestData->set('f-status', 698);
            }
        }
        
        if (!$this->hasTagFilterPropertyValue('age', $requestData->get('f-age'))) {
            $requestData->set('f-age', 724);
        }
        
        $values = [];
        foreach ($requestData as $k => $v) {
            if (0 !== strpos($k, \View\Product\FilterForm::$name)) continue;
            $parts = array_pad(explode('-', $k), 3, null);

            if (!isset($values[$parts[1]])) {
                $values[$parts[1]] = [];
            }
            if (('from' == $parts[2]) || ('to' == $parts[2])) {
                $values[$parts[1]][$parts[2]] = $v;
            } else {
                $values[$parts[1]][] = $v;
            }
        }

        // filter values
        if ($request->get('scrollTo')) {
            // TODO: SITE-2218 сделать однотипные фильтры для ювелирки и неювелирки
            $values = (array)$request->get(\View\Product\FilterForm::$name, []);
        }

        return $values;
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     * @param \Http\Request $request
     * @param \Model\Shop\Entity|null $shop
     * @return \Model\Product\Filter
     */
    private function getFilter(array $filters, \Http\Request $request, $shop = null) {
        // добывание фильтров из http-запроса
        $values = $this->getFilterFromUrl($request);

        // Пока не нужно
//        $values = $this->deleteNotExistsValues($values, $filters);

        //если есть фильтр по магазину
        if ($shop) {
            /** @var \Model\Shop\Entity $shop */
            $values['shop'] = $shop->getId();
        }

        // проверяем есть ли в запросе фильтры
        if ($values) {
            // полнотекстовый поиск через сфинкс
            if (\App::config()->sphinx['showListingSearchBar']) {
                $sphinxFilter = isset($values['text']) ? $values['text'] : null;

                if ($sphinxFilter) {
                    $clientV2 = \App::coreClientV2();
                    $result = null;
                    $clientV2->addQuery('search/normalize', [], ['request' => $sphinxFilter], function ($data) use (&$result) {
                        $result = $data;
                    });
                    $clientV2->execute();

                    if(is_array($result)) {
                        $values['text'] = implode(' ', $result);
                    } else {
                        unset($values['text']);
                    }
                }

                $sphinxFilterData = [
                    'filter_id'     => 'text',
                    'type_id'       => \Model\Product\Filter\Entity::TYPE_STRING,
                ];
                $sphinxFilter = new \Model\Product\Filter\Entity($sphinxFilterData);
                array_push($filters, $sphinxFilter);
            }
        }

        $productFilter = new \Model\Product\Filter($filters, false, false, $shop);
        $productFilter->setValues($values);

        return $productFilter;
    }

    /**
     * @param array $values
     * @param \Model\Product\Filter\Entity[] $filters
     * @return array
     */
    private function deleteNotExistsValues(array $values, array $filters) {
        // SITE-4818 Не учитывать фильтр при переходе в подкатегорию, если такового не существует
        foreach ($values as $propertyId => $propertyValues) {
            $isPropertyExistsInFilter = false;

            foreach ($filters as $property) {
                if ($property->getId() === $propertyId) {
                    $isPropertyExistsInFilter = true;
                    if ($property->getTypeId() === \Model\Product\Filter\Entity::TYPE_LIST) {
                        $optionIds = [];
                        foreach ($property->getOption() as $option) {
                            $optionIds[] = (string)$option->getId();
                        }

                        foreach ($propertyValues as $i => $value) {
                            if (!in_array((string)$value, $optionIds, true)) {
                                unset($values[$propertyId][$i]);
                            }
                        }

                        if (!count($values[$propertyId])) {
                            unset($values[$propertyId]);
                        }
                    }

                    break;
                }
            }

            if (!$isPropertyExistsInFilter) {
                unset($values[$propertyId]);
            }
        }

        return $values;
    }
}