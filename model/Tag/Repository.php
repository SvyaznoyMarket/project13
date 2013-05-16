<?php

namespace Model\Tag;

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
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('tag/get',
            [
                'slug'   => $token,
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
     * @param $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('tag/get',
            [
                'id'     => $id,
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
     * Получает SEO-данные для тэга из json
     * Возвращает массив с SEO-данными
     *
     * @param $category
     * @param $folder
     * @param $brand
     * @return array
     */
    public static function getSeoJson($tag) {
        // формируем запрос к апи и получаем json с SEO-данными
        $seoJson = [];

        $dataStore = \App::dataStoreClient();
        $query = sprintf('seo/tag/%s.json', $tag->getToken());
        $dataStore->addQuery($query, [], function ($data) use (&$seoJson) {
            if($data) $seoJson = $data;
        });
        
        // данные для шаблона
        $patterns = [
            'тэг' => [$tag->getName()],
            'сайт'      => null,
        ];

        $dataStore->addQuery('inflect/сайт.json', [], function($data) use (&$patterns) {
            if ($data) $patterns['сайт'] = $data;
        });
        $dataStore->addQuery(sprintf('inflect/tag/%s.json', $tag->getToken()), [], function($data) use (&$patterns) {
            if ($data) $patterns['тэг'] = $data;
        });

        $dataStore->execute();

        if(!empty($seoJson)) {
            $replacer = new \Util\InflectReplacer($patterns);
            foreach ($seoJson as $property => $value) {
                if(is_array($value)) {
                    foreach ($value as $key => $part) {
                        if(is_array($part)) continue;
                        if ($partValue = $replacer->get($part)) {
                            $seoJson[$property][$key] = $partValue;
                        }
                    }
                } else {
                    if ($value = $replacer->get($seoJson[$property])) {
                        $seoJson[$property] = $value;
                    }
                }
            }
        }

        return empty($seoJson) ? [] : $seoJson;
    }

}