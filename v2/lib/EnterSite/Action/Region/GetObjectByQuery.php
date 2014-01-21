<?php

namespace EnterSite\Action\Region;

use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class GetObjectByQuery {
    use \EnterSite\ConfigTrait;

    public function execute(Query $query) {
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