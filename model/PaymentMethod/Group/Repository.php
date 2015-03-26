<?php

namespace Model\PaymentMethod\Group;

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
     * @param \Model\Region\Entity $region
     * @param array $filters
     * @param array $data
     * @param $done
     * @param null $fail
     */
    public function prepareCollection(\Model\Region\Entity $region = null, array $filters = [], array $data = [], $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $param = [
            'geo_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
            'client_id' => 'site',
        ];

        if (!empty($filters)) {
            $param = array_merge($param, $filters);
        }

        $this->client->addQuery('payment-method/get-group', $param, $data, $done, $fail);
    }
}