<?php

namespace Model\Game\Bandit;

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
     * @return array
     */
    public function getBanditJson() {
        $dataStore = \App::dataStoreClient();

        $banditJson = [];
        try {
            $banditJson = $dataStore->query('game/bandit.json');

            if (!(bool)$banditJson) {
                throw new \Exception(sprintf('Конфиг %s в cms-e пустой', 'game/bandit.json'));
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);

            $configFile = \App::config()->dataDir . '/data-store/game/bandit.json';
            $content = file_exists($configFile) ? file_get_contents($configFile) : null;
            if ((bool)$content) {
                $banditJson = (array)json_decode($content);
            }
        }

        return $banditJson;
    }
} 