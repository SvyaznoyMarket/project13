<?php

namespace EnterSite\Repository;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Region {
    use ConfigTrait;

    /**
     * @param Http\Request $request
     * @return int
     */
    public function getIdByHttpRequest(Http\Request $request) {
        $config = $this->getConfig()->region;

        $id = (int)$request->cookie[$config->cookieName] ?: $config->defaultId;

        return $id;
    }

    /**
     * @param Query $query
     * @return Model\Region
     */
    public function getObjectByQuery(Query $query) {
        $region = null;

        $item = $query->getResult();
        if (!$item) {
            // TODO: журналирование
            $region = new Model\Region();
            $region->id = $this->getConfig()->region->defaultId;
            $region->name = 'Москва*';
        } else {
            $region = new Model\Region($item);
        }

        return $region;
    }
}