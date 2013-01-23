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

        $data = $this->client->query('payment-method/get-credit-bank', array(
            'id'     => array($id),
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = reset($data);

        return $data ? new \Model\CreditBank\Entity($data) : null;
    }

    /**
     * @return Entity[]
     */
    public function getCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $result = $this->client->query('payment-method/get-credit-bank', array(
            'geo_id' => \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($result as $item) {
            $collection[] = new \Model\CreditBank\Entity($item);
        }

        return $collection;
    }

    public function prepareCollection($done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('payment-method/get-credit-bank', array(
            'geo_id' => \App::user()->getRegion()->getId(),
        ), array(), $done, $fail);
    }
}
