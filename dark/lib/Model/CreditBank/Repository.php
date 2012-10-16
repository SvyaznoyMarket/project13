<?php

namespace Model\CreditBank;

class Repository
{
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @static
     * @param string $categoryToken
     * @return string
     */
    static public function getCreditTypeByCategoryToken($categoryToken) {
        $knownCategories = array('electronics', 'sport', 'appliances', 'do_it_yourself', 'furniture', 'household');
        if (!in_array($categoryToken, $knownCategories)) {
            return 'another';
        }
        return $categoryToken;
    }


    /**
     * @param string $token
     * @return null|CreditBankEntity
     */
    public function getEntityById($id)
    {
        if (!$id) {
            return null;
        }
        $params = array('id' => array($id), 'geo_id' => \App::user()->getRegion()->getId());
        $result = $this->client->query('payment-method.get-credit-bank', $params);

        if (empty($result) || !is_array($result) || empty($result[0])) {
            return null;
        }

        $creditBank = new \Model\CreditBank\Entity($result[0]);

        return $creditBank;
    }
    /**
     * @param string $token
     * @return null|CreditBankEntity
     */
    public function getCollection()
    {
        $params = array('geo_id' => \App::user()->getRegion()->getId());

        $result = $this->client->query('payment-method.get-credit-bank', $params);

        if (empty($result) || !is_array($result)) {
            return null;
        }

        $creditBank = array();
        foreach ($result as $item) {
            $creditBank[] = new \Model\CreditBank\Entity($item);
        }

        return $creditBank;
    }

}
