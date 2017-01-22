<?php

namespace Controller\Gift\ProductCategory;

use Session\AbTest\ABHelperTrait;

class Action {
    use ABHelperTrait;
    
    protected $pageTitle;

    /**
     * @param \Http\Request $request
     * @param string|null $page
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function category(\Http\Request $request, $page = null) {
        if (!isset($page) && $request->query->get('page')) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => (int)$request->query->get('page'),
            ]), 301);
        }

        if (isset($page) && $page <= 1) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([], ['page'], $request->routeName), 301);
        }

        // Например, ести url = .../page-02
        if (isset($page) && (string)(int)$page !== $page) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => (int)$page,
            ]), 301);
        }

        $page = (int)$page ?: 1;

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ($data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
            
            $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);
        }

        // подготовка 2-го пакета запросов

        // запрашиваем фильтры
        /** @var $filters \Model\Product\Filter\Entity[] */
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollection($this->getFilterFromUrlDump($request), function($data) use (&$filters) {
            foreach ($data as $item) {
                $filters[] = new \Model\Product\Filter\Entity($item);
            }
        });

        \RepositoryManager::menu()->prepareCollection(function($data) use (&$categories) {
            $categories = $data;
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        $this->createTagFilterProperties($filters);
        $this->createCategoryFilterProperties($filters, $categories);

        $shop = null;
        try {
            if (\App::request()->get('shop') && \App::config()->shop['enabled']) {
                $shop = \RepositoryManager::shop()->getEntityById( \App::request()->get('shop') );
            }
        } catch (\Exception $e) {
            \App::logger()->error(sprintf('Не удалось отфильтровать товары по магазину #%s', \App::request()->get('shop')));
        }

        // фильтры
        $productFilter = $this->getProductFilter($filters, $request, $shop);

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);

        $productPager = $this->getProductPager($productFilter, $productSorting, $page);

        // проверка на максимально допустимый номер страницы
        if (1 != $productPager->getPage() && $productPager->getPage() - $productPager->getLastPage() > 0) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        $helper = new \Helper\TemplateHelper();

        $listViewData = (new \View\Product\ListAction())->execute(
            $helper,
            $productPager,
            [],
            null,
            true,
            4,
            \Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION,
            ['name' => 'gift'],
            null,
            [],
            true
        );

        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            $data = [
                'filters'        => $this->getFiltersForAjaxResponse($productFilter),
                'list'           => $listViewData,
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
                'request' => [
                    'route' => [
                        'name' => \App::request()->routeName,
                        'pathVars' => \App::request()->routePathVars->all(),
                    ],
                ],
            ];

            return new \Http\JsonResponse($data);
        }

        $pageView = new \View\Gift\ProductCategory\LeafPage();
        $pageView->setParam('productFilter', $productFilter);
        $pageView->setParam('productPager', $productPager);
        $pageView->setParam('productSorting', $productSorting);
        $pageView->setParam('listViewData', $listViewData);
        $pageView->setGlobalParam('shop', $shop);

        return new \Http\Response($pageView->show());
    }

    /**
     * @return array
     */
    private function getFiltersForAjaxResponse(\Model\Product\Filter $productFilter) {
        $filters = [];

        $priceFilterProperty = $productFilter->getPriceProperty();
        if ($priceFilterProperty) {
            $filters['price'] = ['min' => $priceFilterProperty->getMin(), 'max' => $priceFilterProperty->getMax()];
        }

        return $filters;
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

    private function createCategoryFilterProperties(array &$filters, $categories) {
        $property = new \Model\Product\Filter\Entity();
        $property->setId('category');
        $property->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);
        $property->setIsMultiple(true);

        if (isset($categories['item']) && is_array($categories['item'])) {
            foreach ($categories['item'] as $item) {
                if (isset($item['source']['id']) && isset($item['name'])) {
                    $property->addOption(new \Model\Product\Filter\Option\Entity(['id' => $item['source']['id'], 'name' => $item['name']]));
                }
            }
        }

        $filters[] = $property;
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     * @param \Http\Request $request
     * @param \Model\Shop\Entity|null $shop
     * @return \Model\Product\Filter
     */
    private function getProductFilter(array $filters, \Http\Request $request, $shop = null) {
        // добывание фильтров из http-запроса
        $values = $this->getFilterFromUrl($request);

        // Пока не нужно
//        $values = $this->deleteNotExistsValues($values, $filters);

        //если есть фильтр по магазину
        if ($shop) {
            /** @var \Model\Shop\Entity $shop */
            $values['shop'] = $shop->getId();
        }

        $productFilter = new \Model\Product\Filter($filters, $shop);
        $productFilter->setValues($values);

        return $productFilter;
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
    private function getFilterFromUrlDump(\Http\Request $request) {
        $tagDump = ['tag_and', 1, []];
        $filterDump = [];
        foreach ($this->getFilterFromUrl($request) as $name => $values) {
            if (in_array($name, ['holiday', 'sex', 'status', 'age'], true)) {
                $tagDump[2] = array_merge($tagDump[2], $values);
            } else if ('price' === $name) {
                // Игнорируем фильтр по цене, т.к. для корректного рассчёта мин. и макс. цены методом listing/filter фильтр по цене не должет передаваться в данный метод (данное поведение метода listing/filter является недоработкой)
            } else if (isset($values['from']) || isset($values['to'])) {
                $filterDump[] = [
                    $name,
                    2,
                    isset($values['from']) ? $values['from'] : null,
                    isset($values['to']) ? $values['to'] : null
                ];
            } else {
                $filterDump[] = [$name, 1, $values];
            }
        }

        $filterDump[] = $tagDump;
        $filterDump[] = ['is_view_list', 1, [true]];

        return $filterDump;
    }

    /**
     * @return array
     */
    private function getFilterFromUrl(\Http\Request $request) {
        // добывание фильтров из http-запроса
        if ('POST' == $request->getMethod()) {
            $params = clone $request->request;
        } else {
            $params = clone $request->query;
        }

        $this->setDefaultValues($params);

        $values = [];
        foreach ($params as $k => $v) {
            if (0 !== strpos($k, \View\Product\FilterForm::$name)) {
                continue;
            }

            $parts = array_pad(explode('-', $k), 3, null);

            if ('from' == $parts[2] || 'to' == $parts[2]) {
                $values[$parts[1]][$parts[2]] = $v;
            } else {
                if ($parts[1] === 'category') {
                    $v = array_filter(array_map('trim', explode(',', $v)));
                    if ($v) {
                        $values[$parts[1]] = isset($values[$parts[1]]) ? array_merge($values[$parts[1]], $v) : $v;
                    }
                } else {
                    $values[$parts[1]][] = $v;
                }
            }
        }

        foreach ($values as $k => $v) {
            if (isset($v['from']) && isset($v['to'])) {
                if ($v['from'] > $v['to']) {
                    $values[$k]['from'] = $v['to'];
                }
            }
        }

        return $values;
    }

    /** Начальные параметры фильтров
     * @param \Http\ParameterBag $params
     */
    private function setDefaultValues(\Http\ParameterBag $params) {
        $isSubmitted = (bool)$params->get('f-holiday');

        if (!$this->hasTagFilterPropertyValue('holiday', $params->get('f-holiday'))) {
            $params->set('f-holiday', 739);
        }

        if (!$this->hasTagFilterPropertyValue('sex', $params->get('f-sex'))) {
            $params->set('f-sex', 687);
            /*
            if ($params->get('f-holiday') == 738) {
                $params->set('f-sex', 688);
            } else {
                $params->set('f-sex', 687);
            }
            */
        }

        if (!$this->hasTagFilterPropertyValue('status', $params->get('f-status'))) {
            if ($params->get('f-sex') == 687) {
                $params->set('f-status', 689);
            } else {
                $params->set('f-status', 698);
            }
        }

        if (!$this->hasTagFilterPropertyValue('age', $params->get('f-age'))) {
            $params->set('f-age', 724);
        }

        if (!$this->hasCategoryInQueryParams($params) && !$isSubmitted && $params->get('f-holiday') == 737 && $params->get('f-sex') == 687 && $params->get('f-status') == 689) {
            $params->set('f-category-ukrasheniya_i_chasi', 923);
            $params->set('f-category-parfyumeriya_i_kosmetika', 2545);
        }
    }

    private function hasCategoryInQueryParams(\Http\ParameterBag $params) {
        foreach ($params as $name => $value) {
            if (strpos($name, 'f-category-') === 0) {
                return true;
            }
        }

        return false;
    }

    private function getTagFilterPropertyValues() {
        return [
            'holiday' => [
                ['id' => 737, 'name' => '14 февраля'],
                ['id' => 738, 'name' => '23 февраля'],
                ['id' => 739, 'name' => '8 марта'],
                ['id' => 707, 'name' => 'День рождения'],
                ['id' => 708, 'name' => 'Юбилей'],
                ['id' => 709, 'name' => 'День свадьбы'],
                ['id' => 710, 'name' => 'Новоселье'],
                ['id' => 711, 'name' => 'Благодарность'],
                ['id' => 706, 'name' => 'Новый Год'],
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
     * @param \Model\Product\Filter\Entity[] $filters
     * @return array
     */
    // Пока данный метод не нужен
    /*
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
    */

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

        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $productCount = 0;
        $repository->prepareIteratorByFilter(
            $this->getProductFilterDump($productFilter),
            $productSorting->dump(),
            $offset,
            $limit,
            $region,
            function($data) use (&$products, &$productCount) {
                if (isset($data['list'][0])) {
                    $products = array_map(function($productId) { return new \Model\Product\Entity(['id' => $productId]); }, $data['list']);
                }

                if (isset($data['count'])) {
                    $productCount = (int)$data['count'];
                }
            }
        );
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        if ($products) {
            $repository->prepareProductQueries($products, 'media label brand category');
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);
        }

        foreach ($products as $product) {
            $product->setLink($product->getLink() . (strpos($product->getLink(), '?') === false ? '?' : '&') . http_build_query(['sender' => ['name' => 'gift']]));
        }

        \RepositoryManager::review()->prepareScoreCollection($products, function($data) use(&$products) {
            if (isset($data['product_scores'][0])) {
                \RepositoryManager::review()->addScores($products, $data);
            }
        });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($itemsPerPage);
        return $productPager;
    }
}