<?php

namespace Model\Product\Filter;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
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

        $collection = [];
        $client->addQuery('listing/filter', $params, [], function ($data) use (&$collection) {
            foreach ($data as $item) {
                $collection[] = new Entity($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Region\Entity           $region
     * @param                                $done
     * @param                                $fail
     */
    public function prepareCollectionByCategory(\Model\Product\Category\Entity $category, \Model\Region\Entity $region = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'category_id' => $category->getId(),
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }
        $this->client->addQuery('listing/filter', $params, [], $done, $fail);
    }
}