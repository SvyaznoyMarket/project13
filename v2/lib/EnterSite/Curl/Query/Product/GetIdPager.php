<?php

namespace EnterSite\Curl\Query\Product;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetIdPager extends Query {
    use CoreQueryTrait;

    /** @var array */
    protected $result;

    /**
     * @param array $filterData
     * @param Model\Product\Sorting $sorting
     * @param string|null $regionId
     * @param $offset
     * @param $limit
     * @param Model\Product\Catalog\Config|null $catalogConfig
     */
    public function __construct(array $filterData, Model\Product\Sorting $sorting = null, $regionId = null, $offset = null, $limit = null, $catalogConfig = null) {
        $sortingData = []; // TODO: вынести в Repository\Product\Sorting::dumpObjectList
        if ($sorting) {
            if (('default' == $sorting->token) && $catalogConfig && (bool)$catalogConfig->sortings) {
                // специальная сортировка
                $sortingData = $catalogConfig->sortings;
            } else {
                $sortingData = [$sorting->token => $sorting->direction];
            }
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
        if ($regionId) {
            $this->url->query['region_id'] = $regionId;
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