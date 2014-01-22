<?php

namespace EnterSite\Repository\Product\Catalog;

use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Config {
    use ConfigTrait;

    public function getObjectByQuery(Query $query) {
        $config = null;

        $item = $query->getResult();
        if ($item) {
            $config = new Model\Product\Catalog\Config($item);
        }

        return $config;
    }
}