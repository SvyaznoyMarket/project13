<?php

namespace Model\Order\BonusCard;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function prepareCollection($data = [], $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [];
        $region = \App::user()->getRegion();
        if ($region) {
            $params['geo_id'] = $region->getId();
        }

        $this->client->addQuery('payment-method/get-bonus-card', $params, $data, $done, $fail);
    }

    public function getCollection($data = []) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        try {
            $params = [];
            $region = \App::user()->getRegion();
            if ($region) {
                $params['geo_id'] = $region->getId();
            }

            $result = $client->query('payment-method/get-bonus-card', $params, $data);

            foreach ($result as $item) {
                $collection[] = new Entity($item);
            }

        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
        }

        return $collection;
    }
} 