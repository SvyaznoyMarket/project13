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
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $params = [
            'category_id' => $category->getId(),
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }

        // SITE-5207 Временно исключить из выдачи сайта партнёрские товары-слоты
        $params['filter']['filters'][] = ['exclude_partner_type', 1, \Model\Product\BasicEntity::PARTNER_OFFER_TYPE_SLOT];

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
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [];

        $params['region_id'] = \App::user()->getRegion()->getId();

        if (!empty($filters)) {
            $params['filter']['filters'] = $filters;
        }

        // SITE-5207 Временно исключить из выдачи сайта партнёрские товары-слоты
        $params['filter']['filters'][] = ['exclude_partner_type', 1, \Model\Product\BasicEntity::PARTNER_OFFER_TYPE_SLOT];

        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Region\Entity           $region
     * @param array                          $filters
     * @param                                $done
     * @param                                $fail
     */
    public function prepareCollectionByCategory(\Model\Product\Category\Entity $category, \Model\Region\Entity $region = null, array $filters = [], $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'category_id' => $category->getId(),
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }

        if (!empty($filters)) {
            $params['filter']['filters'] = $filters;
        }

        // SITE-5207 Временно исключить из выдачи сайта партнёрские товары-слоты
        $params['filter']['filters'][] = ['exclude_partner_type', 1, \Model\Product\BasicEntity::PARTNER_OFFER_TYPE_SLOT];

        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }

    /**
     * @param string               $searchText
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionBySearchText($searchText, \Model\Region\Entity $region = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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

        // SITE-5207 Временно исключить из выдачи сайта партнёрские товары-слоты
        $params['filter']['filters'][] = ['exclude_partner_type', 1, \Model\Product\BasicEntity::PARTNER_OFFER_TYPE_SLOT];

        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }


    /**
     * @param \Model\Tag\Entity         $tag
     * @param \Model\Region\Entity      $region
     * @param function                  $done
     * @param function|null             $fail
     */
    public function prepareCollectionByTag(\Model\Tag\Entity $tag, \Model\Region\Entity $region = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'filter' => [
                'filters' => [
                    //['text', 3, $tag->getName()], // тоже нужно!
                    ['tag', 1, $tag->getId()],
                ],
            ],
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }

        // SITE-5207 Временно исключить из выдачи сайта партнёрские товары-слоты
        $params['filter']['filters'][] = ['exclude_partner_type', 1, \Model\Product\BasicEntity::PARTNER_OFFER_TYPE_SLOT];

        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }
}