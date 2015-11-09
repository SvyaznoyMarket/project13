<?php

namespace EnterApplication\Action;

use EnterQuery as Query;

trait ActionTrait
{
    /**
     * Получает запрос региона либо по id, либо по ip
     *
     * @param $regionId
     * @return Query\Region\GetById|Query\Region\GetByIp
     */
    protected function getRegionQuery($regionId)
    {
        /** @var Query\Region\GetById|Query\Region\GetByIp $query */
        $query = null;
        if ($regionId) {
            $query = (new Query\Region\GetById($regionId))->prepare();
        } else if (
            \App::config()->region['autoresolve']
            && (false === strpos(\App::request()->headers->get('user-agent'), 'http://yandex.com/bots')) // SITE-4393
        ) {
            $query = (new Query\Region\GetByIp(\App::request()->getClientIp()))->prepare();
        }
        if (!$query) {
            $query = (new Query\Region\GetById(\App::config()->region['defaultId']))->prepare();
        }

        return $query;
    }

    /**
     * Проверяет ответ запроса на пустоту:
     * если регион не получен - подменяет ответ с регионом по умолчанию
     *
     * @param Query\Region\GetById|null  $query
     */
    protected function checkRegionQuery(&$query = null)
    {
        if (!$query || empty($query->response->region['id'])) {
            $query = new Query\Region\GetById(\App::config()->region['defaultId']);
            $query->response->region = \App::dataStoreClient()->query('/region-default.json')['result'][0];
        }
    }
}