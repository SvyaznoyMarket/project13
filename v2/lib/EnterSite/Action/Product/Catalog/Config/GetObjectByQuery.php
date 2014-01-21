<?php

namespace EnterSite\Action\Product\Catalog\Config;

use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class GetObjectByQuery {
    use ConfigTrait;

    public function execute(Query $query) {
        $config = null;

        $item = $query->getResult();
        if ($item) {
            $config = new Model\Product\Catalog\Config($item);
        }

        return $config;
    }
}