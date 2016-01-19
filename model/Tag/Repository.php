<?php

namespace Model\Tag;

class Repository {
    /**
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = \App::scmsClient();

        $entity = null;
        $client->addQuery('seo/tags',
            [
                'slugs' => [$token],
            ],
            [],
            function ($data) use (&$entity) {
                if (!is_array($data)) return;

                $data = reset($data);
                if ($data) {
                    $entity = new Entity($data);
                }
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }
}