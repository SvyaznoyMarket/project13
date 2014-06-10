<?php

namespace EnterSite\Repository\Product;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Model;

class Filter {
    use ConfigTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @param Http\Request $request
     * @return Model\Product\RequestFilter[]
     */
    public function getRequestObjectListByHttpRequest(Http\Request $request) {
        $filters = [];

        foreach ($request->query as $key => $value) {
            if (
                is_scalar($value)
                && (
                    (0 === strpos($key, 'f-'))
                    || (0 === strpos($key, 'tag-'))
                    || (in_array($key, ['shop', 'category']))
                )
            ) {
                $filter = new Model\Product\RequestFilter();
                $filter->name = $key;
                $filter->value = $value;

                $keyParts = array_pad(explode('-', $key), 3, null);
                $filter->token = $keyParts[1] ?: $keyParts[0];
                $filter->optionToken = $keyParts[2];

                $filters[] = $filter;
            }
        }

        return $filters;
    }

    /**
     * @param Model\Product\Category $category
     * @return Model\Product\RequestFilter
     */
    public function getRequestObjectByCategory(Model\Product\Category $category) {
        $filter = new Model\Product\RequestFilter();
        $filter->token = 'category';
        $filter->name = 'category';
        $filter->value = $category->id;

        return $filter;
    }

    /**
     * @param string $searchPhrase
     * @return Model\Product\RequestFilter
     */
    public function getRequestObjectBySearchPhrase($searchPhrase) {
        $filter = new Model\Product\RequestFilter();
        $filter->token = 'q';
        $filter->name = 'q';
        $filter->value = $searchPhrase;

        return $filter;
    }

    /**
     * Возвращает фильтр из http-запроса, который относится к категории товара
     *
     * @param Model\Product\RequestFilter[] $filters
     * @return Model\Product\RequestFilter
     */
    public function getCategoryRequestObjectByRequestList($filters) {
        $return = null;

        foreach ($filters as $filter) {
            if ('category' == $filter->token) {
                $return = $filter;
                break;
            }
        }

        return $return;
    }

    /**
     * @param Query $query
     * @return Model\Product\Filter[]
     */
    public function getObjectListByQuery(Query $query) {
        $filters = [];

        try {
            foreach ($query->getResult() as $item) {
                $filters[] = new Model\Product\Filter($item);
            }
        } catch (\Exception $e) {
            $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['repository']]);

            trigger_error($e, E_USER_ERROR);
        }

        return $filters;
    }

    /**
     * @param Model\Product\Category[] $categories
     * @return Model\Product\Filter[]
     */
    public function getObjectListByCategoryList(array $categories) {
        $filters = [];

        $categoryOptionData = [];
        foreach ($categories as $category) {
            $categoryOptionData[] = [
                'id'       => $category->id,
                'token'    => $category->id,
                'name'     => $category->name,
                'quantity' => $category->productCount,
                'image'    => $category->image,
            ];
        }
        $filters[] = new Model\Product\Filter([
            'filter_id' => 'category',
            'name'      => 'Категории',
            'type_id'   => Model\Product\Filter::TYPE_LIST,
            'options'   => $categoryOptionData,
        ]);

        return $filters;
    }

    /**
     * @param Model\Product\RequestFilter[] $requestFilters
     * @return array
     */
    public function dumpRequestObjectList(array $requestFilters) {
        $return = [];

        // TODO: перевести все на f-
        $filterData = [];
        foreach ($requestFilters as $requestFilter) {
            $key = $requestFilter->name;
            $value = $requestFilter->value;

            if (0 === strpos($key, 'f-')) {
                $parts = array_pad(explode('-', $key), 3, null);

                if (!isset($filterData[$parts[1]])) {
                    $filterData[$parts[1]] = [
                        'value' => [],
                    ];
                }

                if (('from' == $parts[2]) || ('to' == $parts[2])) {
                    $filterData[$parts[1]]['value'][$parts[2]] = $value;
                } else {
                    $filterData[$parts[1]]['value'][] = $value;
                }
            } else if (0 === strpos($key, 'tag-')) {
                if (!isset($filterData['tag'])) {
                    $filterData['tag'] = [
                        'value' => [],
                    ];
                }

                $filterData['tag']['value'][] = $value;
            } else if (in_array($key, ['category', 'shop', 'q'])) {
                if (!isset($filterData[$key])) {
                    $filterData[$key] = [
                        'value' => [],
                    ];
                }
                $filterData[$key]['value'][] = $value;
            }
        }

        foreach ($filterData as $key => $filter) {
            if (isset($filter['value']['from']) || isset($filter['value']['to'])) {
                $return[] = [$key, 2, isset($filter['value']['from']) ? $filter['value']['from'] : null, isset($filter['value']['to']) ? $filter['value']['to'] : null];
            } else if ('q' == $key) {
                $return[] = ['text', 3, reset($filter['value'])];
            } else {
                $return[] = [$key, 1, $filter['value']];
            }
        }

        return $return;
    }
}