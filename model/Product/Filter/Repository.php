<?php

namespace Model\Product\Filter;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Search\Client $client
     */
    public function __construct(\Search\Client $client) {
        $this->client = $client;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Region\Entity           $region
     * @return array
     */
    public function getCollectionByCategory(\Model\Product\Category\Entity $category, \Model\Region\Entity $region = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $params = [
            'category_id' => $category->getId(),
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }

        $collection = [];
        $client->addQuery('listing/filter', $params, [], function ($data) use (&$collection) {
            foreach ($data as $item) {
                $collection[] = new Entity($item);
            }
        });

        $client->execute();

        return $collection;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Region\Entity           $region
     * @param array                          $filters
     * @param                                $done
     * @param                                $fail
     */
    public function prepareCollection(array $filters = [], $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [];

        $params['region_id'] = \App::user()->getRegion()->getId();

        if (!empty($filters)) {
            $params['filter']['filters'] = $filters;
        }

        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Region\Entity           $region
     * @param array                          $filters
     * @param                                $done
     * @param                                $fail
     */
    public function prepareCollectionByCategory(\Model\Product\Category\Entity $category = null, \Model\Region\Entity $region = null, array $filters = [], $done = null, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [];

        if ($category) {
            if ($category->getId()) {
                $params['category_id'] = $category->getId();
            }

            if ($category->getToken() === \Model\Product\Category\Entity::FAKE_SHOP_TOKEN) {
                $params['bu'] = 1;
            }
        }

        if ($region) {
            $params['region_id'] = $region->getId();
        }

        if (!empty($filters)) {
            $params['filter']['filters'] = $filters;
        }

        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }

    /**
     * @param string               $searchText
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionBySearchText($searchText, \Model\Region\Entity $region = null, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'filter' => [
                'filters' => [
                    ['text', 3, $searchText],
                ],
            ],
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }

        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }


    /**
     * @param \Model\Tag\Entity         $tag
     * @param \Model\Region\Entity      $region
     * @param \Closure                  $done
     * @param \Closure|null             $fail
     */
    public function prepareCollectionByTag(\Model\Tag\Entity $tag, \Model\Region\Entity $region = null, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'filter' => [
                'filters' => [
                    //['text', 3, $tag->getName()], // тоже нужно!
                    ['tag', 1, $tag->id],
                ],
            ],
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }

        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Brand\Entity|null $brand
     * @param \Http\Request $request
     * @param \Model\Shop\Entity|null $shop
     * @param \Closure|null $isPreserveImagesForProperty
     * @return \Model\Product\Filter
     */
    public function createProductFilter(array $filters, \Model\Product\Category\Entity $category = null, \Model\Brand\Entity $brand = null, \Http\Request $request, $shop = null, $isPreserveImagesForProperty = null) {
        // регион для фильтров
        $region = \App::user()->getRegion();

        // добывание фильтров из http-запроса
        $values = $this->getFilterValuesFromHttpRequest($request);

        if (\App::request()->get('instore')) {
            $values['instore'] = 1; // TODO SITE-2403 Вернуть фильтр instore
            $values['label'][] = 1; // TODO Костыль для таска: SITE-2403 Вернуть фильтр instore
        }
        if ($brand) {
            $values['brand'] = [
                $brand->getId(),
            ];
        }

        //если есть фильтр по магазину
        if ($shop) {
            $values['shop'] = [$shop->getId()];
        }

        $values = $this->deleteNotExistsValues($values, $filters);
        $values = $this->correctPriceRangeOutside($values, $filters);

        // проверяем есть ли в запросе фильтры
        if ($values) {
            // проверяем есть ли в запросе фильтры, которых нет в текущей категории (фильтры родительских категорий)
            /** @var $exists Ид фильтров текущей категории */
            $exists = array_map(function($filter) { /** @var $filter \Model\Product\Filter\Entity */ return $filter->getId(); }, $filters);
            /** @var $diff Ид фильтров родительских категорий */
            $diff = array_diff(array_keys($values), $exists);
            if ((bool)$diff && $category) {
                foreach ($category->getAncestor() as $ancestor) {
                    try {
                        /** @var $ancestorFilters \Model\Product\Filter\Entity[] */
                        $ancestorFilters = [];
                        \RepositoryManager::productFilter()->prepareCollectionByCategory($ancestor, $region, function($data) use (&$ancestorFilters) {
                            foreach ($data as $item) {
                                $ancestorFilters[] = new \Model\Product\Filter\Entity($item);
                            }
                        });
                        \App::coreClientV2()->execute();
                    } catch (\Exception $e) {
                        $ancestorFilters = [];
                    }
                    foreach ($ancestorFilters as $filter) {
                        if (false === $i = array_search($filter->getId(), $diff)) continue;

                        // скрываем фильтр в списке
                        $filter->setIsInList(false);
                        $filters[] = $filter;
                        unset($diff[$i]);
                        if (!(bool)$diff) break;
                    }
                    if (!(bool)$diff) break;
                }
            }
        }

        $productFilter = new \Model\Product\Filter($filters, $shop);
        $productFilter->setCategory($category);
        $productFilter->setValues($values);

        foreach ($productFilter->getFilterCollection() as $property) {
            if (
                \Model\Product\Filter\Entity::TYPE_LIST == $property->getTypeId()
                && (!in_array($property->getId(), ['shop', 'category']) || ($category && $category->getToken() == \Model\Product\Category\Entity::FAKE_SHOP_TOKEN))
                && !($category && $category->isTyre() && in_array($property->getName(), ['Сезон', 'Ширина', 'Профиль', 'Диаметр'], true))
            ) {
                $property->setIsMultiple(true);
            } else {
                $property->setIsMultiple(false);
            }

            if (!$isPreserveImagesForProperty || !$isPreserveImagesForProperty($property)) {
                foreach ($property->getOption() as $option) {
                    $option->setImageUrl('');
                }
            }
        }

        return $productFilter;
    }

    /**
     * @param \Http\Request $request
     * @param array $excludeFilterNames
     * @return array
     */
    public function getFilterValuesFromHttpRequest(\Http\Request $request, array $excludeFilterNames = []) {
        // добывание фильтров из http-запроса
        $requestData = 'POST' == $request->getMethod() ? $request->request : $request->query;

        $values = [];
        foreach ($requestData as $k => $v) {
            if (0 !== strpos($k, \View\Product\FilterForm::$name)) {
                continue;
            }

            $parts = array_pad(explode('-', $k), 3, null);

            if (!in_array($parts[1], $excludeFilterNames, true)) {
                if ('from' == $parts[2] || 'to' == $parts[2]) {
                    $values[$parts[1]][$parts[2]] = $v;
                } else {
                    if ($parts[1] === 'shop' || $parts[1] === 'category') {
                        $v = array_filter(array_map('trim', explode(',', $v)));
                        if ($v) {
                            $values[$parts[1]] = isset($values[$parts[1]]) ? array_merge($values[$parts[1]], $v) : $v;
                        }
                    } else {
                        $values[$parts[1]][] = $v;
                    }
                }
            }
        }

        // SITE-5017
        foreach ($values as $k => $v) {
            if (isset($v['from']) && isset($v['to'])) {
                if ($v['from'] > $v['to']) {
                    $values[$k]['from'] = $v['to'];
                }
            }
        }

        return $values;
    }

    /**
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

                        foreach ((array)$propertyValues as $i => $value) {
                            if (
                                !in_array((string)$value, $optionIds, true)
                                && !$property->isBrand() // SITE-5844
                            ) {
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

    /**
     * @param \Model\Product\Filter\Entity[] $filters
     * @return array
     */
    private function correctPriceRangeOutside(array $values, array $filters) {
        if (isset($values['price'])) {
            $filter = $this->getFilterById('price', $filters);

            if ($filter) {
                $min = $filter->getMin();
                $max = $filter->getMax();

                if (isset($values['price']['from'])) {
                    if ($values['price']['from'] > $max) {
                        $values['price']['from'] = $max;
                    }
                }

                if (isset($values['price']['to'])) {
                    if ($values['price']['to'] < $min) {
                        $values['price']['to'] = $min;
                    }
                }
            }
        }

        return $values;
    }

    /**
     * @param string $id
     * @param \Model\Product\Filter\Entity[] $filters
     * @return \Model\Product\Filter\Entity|null
     */
    private function getFilterById($id, array $filters) {
        foreach ($filters as $filter) {
            if ($filter->getId() === $id) {
                return $filter;
            }
        }

        return null;
    }
}