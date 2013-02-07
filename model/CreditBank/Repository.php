<?php

namespace Model\CreditBank;

class Repository
{
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @static
     * @param string $categoryToken
     * @return string
     */
    public static function getCreditTypeByCategoryToken($categoryToken) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        return in_array($categoryToken, array('electronics', 'sport', 'appliances', 'do_it_yourself', 'furniture', 'household'))
            ? $categoryToken
            : 'another';
    }


    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!$id) {
            return null;
        }

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('payment-method/get-credit-bank',
            [
                'id'     => array($id),
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity) {
                $data = reset($data);
                $entity = $data ? new Entity($data) : null;
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @return Entity[]
     */
    public function getCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('payment-method/get-credit-bank',
            [
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$collection) {
                foreach ($data as $item) {
                    $collection[] = new Entity($item);
                }
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param callback $done
     * @param callback $fail
     */
    public function prepareCollection($done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('payment-method/get-credit-bank', array(
            'geo_id' => \App::user()->getRegion()->getId(),
        ), [], $done, $fail);
    }
}
