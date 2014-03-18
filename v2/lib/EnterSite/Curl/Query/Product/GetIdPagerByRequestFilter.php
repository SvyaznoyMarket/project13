<?php

namespace EnterSite\Curl\Query\Product;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetIdPagerByRequestFilter extends Query {
    use CoreQueryTrait;

    /** @var array */
    protected $result;

    /**
     * @param Model\Product\RequestFilter[] $filters
     * @param Model\Product\Sorting $sorting
     * @param Model\Region $region
     * @param $offset
     * @param $limit
     */
    public function __construct(array $filters, Model\Product\Sorting $sorting = null, Model\Region $region = null, $offset = null, $limit = null) {
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

        $this->url = new Url();
        $this->url->path = 'v2/listing/list';
        $this->url->query = [
            'filter' => [
                'filters' => $filterData,
                'sort'    => $sortingData,
                'offset'  => $offset,
                'limit'   => $limit,
            ],
        ];
        if ($region) {
            $this->url->query['region_id'] = $region->id;
        }

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