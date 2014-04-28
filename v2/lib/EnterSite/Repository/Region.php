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
     * @return string
     */
    public function getIdByHttpRequestCookie(Http\Request $request) {
        $config = $this->getConfig()->region;

        $id = (string)$request->cookies[$config->cookieName] ?: $config->defaultId;

        return $id;
    }

    /**
     * @param Http\Request $request
     * @return string
     */
    public function getIdByHttpRequestQuery(Http\Request $request) {
        $id = trim((string)$request->query['regionId']);

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
            // TODO: logger
            $region = new Model\Region();
            $region->id = $this->getConfig()->region->defaultId;
            $region->name = 'Москва*';
        } else {
            $region = new Model\Region($item);
        }

        return $region;
    }
}