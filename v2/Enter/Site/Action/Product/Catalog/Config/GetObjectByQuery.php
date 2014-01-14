<?php

namespace Enter\Site\Action\Product\Catalog\Config;

use Enter\Site\ConfigTrait;
use Enter\Curl\Query;
use Enter\Site\Model\Product\Catalog\Config;

class GetObjectByQuery {
    use ConfigTrait;

    public function execute(Query $query) {
        $config = null;

        $item = $query->getResult();
        if ($item) {
            $config = new Config($item);
        }

        return $config;
    }
}