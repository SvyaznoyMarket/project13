<?php

namespace EnterSite\Curl\Query\Product;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Model\Product\RequestFilter;
use EnterSite\Model\Product\Sorting;
use EnterSite\Model\Region;

class GetIdPagerByRequestFilter extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param RequestFilter[] $filters
     * @param Sorting $sorting
     * @param Region $region
     * @param $offset
     * @param $limit
     */
    public function __construct(array $filters, Sorting $sorting = null, Region $region = null, $offset = null, $limit = null) {
        $filterData = [];
        foreach ($filters as $key => $filter) {
            if (isset($filter->value['from']) || isset($filter->value['to'])) {
                $filterData[] = [$key, 2, isset($filter->value['from']) ? $filter->value['from'] : null, isset($filter->value['to']) ? $filter->value['to'] : null];
            } else {
                $filterData[] = [$key, 1, $filter->value];
            }
        }

        $sortingData = [];
        if ($sorting) {
            $sortingData = [$sorting->token => $sorting->direction];
        }

        $params = [
            'filter' => [
                'filters' => $filterData,
                'sort'    => $sortingData,
                'offset'  => $offset,
                'limit'   => $limit,
            ],
        ];
        if ($region) {
            $params['region_id'] = $region->id;
        }

        $this->url = 'listing/list?' . http_build_query($params);

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = (isset($data['list']) && isset($data['count'])) ? $data : [];
    }
}