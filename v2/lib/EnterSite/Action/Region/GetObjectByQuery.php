<?php

namespace EnterSite\Action\Region;

use EnterSite\ConfigTrait;
use Enter\Curl\Query;
use EnterSite\Model\Region;

class GetObjectByQuery {
    use \EnterSite\ConfigTrait;

    public function execute(Query $query) {
        $region = null;

        $item = $query->getResult();
        if (!$item) {
            // TODO: журналирование
            $region = new Region();
            $region->id = $this->getConfig()->region->defaultId;
            $region->name = 'Москва*';
        } else {
            $region = new Region($item);
        }

        return $region;
    }
}