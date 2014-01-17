<?php

namespace EnterSite\Action\Product\Catalog\Config;

use EnterSite\ConfigTrait;
use Enter\Curl\Query;
use EnterSite\Model\Product\Catalog\Config;

class GetObjectByQuery {
    use \EnterSite\ConfigTrait;

    public function execute(Query $query) {
        $config = null;

        $item = $query->getResult();
        if ($item) {
            $config = new Config($item);
        }

        return $config;
    }
}