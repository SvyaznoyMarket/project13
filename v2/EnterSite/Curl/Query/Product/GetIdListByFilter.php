<?php

namespace EnterSite\Curl\Query\Product;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;

class GetIdListByFilter extends Query {
    use \EnterSite\Curl\Query\CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param array $filter
     * @param array $sort
     * @param $offset
     * @param $limit
     * @param $regionId
     */
    public function __construct(array $filter, array $sort, $offset, $limit, $regionId) {
        $this->url = 'listing/list?' . http_build_query([
            'region_id' => $regionId,
            'filter'    => [
                'filters' => $filter,
                'sort'    => $sort,
                'offset'  => $offset,
                'limit'   => $limit,
            ],
        ]);

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