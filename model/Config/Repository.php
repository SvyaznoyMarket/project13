<?php

namespace Model\Config;

class Repository
{
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Scms\Client $client) {
        $this->client = $client;
    }

    /**
     * @param array $keys
     * @param Entity[] $entities
     * @param \Callable|null $filter
     */
    public function prepare(array $keys, &$entities = [], Callable $filter = null) {
        if (!\App::config()->userCallback['enabled']) {
            return;
        }

        $this->client->addQuery(
            'api/parameter/get-by-keys',
            [
                'keys' => $keys,
            ],
            [],
            function($data) use (&$entities, &$filter) {
                if (!is_array($data)) {
                    return;
                }

                foreach ($data as $item) {
                    if (!is_array($item)) continue;

                    $entity = new Entity($item);
                    if (!$filter || $filter($entity)) {
                        $entities[] = $entity;
                    }
                }
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );
    }
}