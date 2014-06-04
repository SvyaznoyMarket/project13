<?php

namespace EnterTerminal\Repository\Product;

use Enter\Http;
use EnterSite\Repository\Product\Filter as BaseRepository;
use EnterSite\Model;

class Filter extends BaseRepository {
    /**
     * @param Http\Request $request
     * @return Model\Product\RequestFilter[]
     */
    public function getRequestObjectListByHttpRequest(Http\Request $request) {
        $filters = [];

        foreach ((array)$request->query['filter'] as $key => $value) {
            foreach ((array)$value as $optionToken => $optionValue) {
                $filter = new Model\Product\RequestFilter();
                $filter->token = $key;
                $filter->name = $key;
                $filter->value = $optionValue;

                $filter->optionToken = $optionToken;

                $filters[] = $filter;
            }
        }

        return $filters;
    }

    /**
     * @param Model\Product\RequestFilter[] $requestFilters
     * @return array
     */
    public function dumpRequestObjectList(array $requestFilters) {
        $return = [];

        $filterData = [];
        foreach ($requestFilters as $requestFilter) {
            $key = $requestFilter->token;
            $value = $requestFilter->value;

            if (!isset($filterData[$key])) {
                $filterData[$key] = [
                    'value' => [],
                ];
            }

            if (('from' == $requestFilter->optionToken) || ('to' == $requestFilter->optionToken)) {
                $filterData[$key]['value'][$requestFilter->optionToken] = $value;
            } else {
                $filterData[$key]['value'][] = $value;
            }
        }

        foreach ($filterData as $key => $filter) {
            if (isset($filter['value']['from']) || isset($filter['value']['to'])) {
                $return[] = [$key, 2, isset($filter['value']['from']) ? $filter['value']['from'] : null, isset($filter['value']['to']) ? $filter['value']['to'] : null];
            } else {
                $return[] = [$key, 1, $filter['value']];
            }
        }

        return $return;
    }

    /**
     * @param Model\Product\Filter[] $filters
     * @param Model\Product\RequestFilter[] $requestFilters
     */
    public function setValueForObjectList(array $filters, array $requestFilters) {
        if (!(bool)$requestFilters) {
            return;
        }

        $filtersByToken =[];
        foreach ($filters as $filter) {
            $filtersByToken[$filter->token] = $filter;
        }

        foreach ($requestFilters as $requestFilter) {
            $filter = isset($filtersByToken[$requestFilter->token]) ? $filtersByToken[$requestFilter->token] : null;
            if (!$filter) {
                continue;
            }

            // FIXME
            $filter->isSelected = true;
            if (!isset($filter->value)) {
                $filter->value;
            }

            if ($requestFilter->optionToken) {
                $filter->value[$requestFilter->optionToken] = $requestFilter->value;
            } else {
                $filter->value[] = $requestFilter->value;
            }
        }
    }
}