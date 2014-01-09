<?php

namespace Enter\Site\Action\Region;

use Enter\Site\ConfigTrait;
use Enter\Curl\Query;
use Enter\Site\Model\Region;

class GetObjectByQuery {
    use ConfigTrait;

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