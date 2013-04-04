<?php

namespace Model\Subscribe\Channel;

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
     * @param \Model\User\Entity $user
     * @return Entity[]
     */
    public function getCollection(\Model\User\Entity $user = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = null;

        $params = [];
        if ($user) {
            $params['token'] = $user->getToken();
        }
        $client->addQuery('subscribe/get-channel', $params,
            [],
            function($data) use(&$collection) {
                foreach ($data as $item) {
                    $collection[] = new Entity($item);
                }
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        return $collection;
    }
}