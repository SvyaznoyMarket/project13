<?php

namespace Model\Banner;

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
     * @param \Model\Region\Entity|null $region
     * @return BannerEntity[]
     */
    public function getCollection(\Model\Region\Entity $region = null) {

        $client = clone $this->client;

        $params = [];
        if ($region) {
            $params['geo_id'] = $region->getId();
        }

        $collection = [];
        $client->addQuery('promo/get', [], [], function ($data) use (&$collection) {
            foreach ($data as $item) {
                $collection[] = new BannerEntity($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param $callback
     */
    public function prepareCollection($callback) {

        $params = [
            'tags' => ['site-web'],
        ];
        \App::scmsClient()->addQuery('api/promo/get', $params, [], $callback);
    }


}