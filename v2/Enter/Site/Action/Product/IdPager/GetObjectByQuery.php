<?php

namespace Enter\Site\Action\Product\IdPager;

use Enter\Site\ConfigTrait;
use Enter\Curl\Query;
use Enter\Site\Model\Product\IdPager;

class GetObjectByQuery {
    use ConfigTrait;

    public function execute(Query $query) {
        $pager = null;

        $item = $query->getResult();
        if ($item) {
            $pager = new IdPager($item);
        }

        return $pager;
    }
}