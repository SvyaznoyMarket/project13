<?php

namespace Enter\Site\Action\Product;

use Enter\Site\ConfigTrait;
use Enter\Curl\Query;
use Enter\Site\Model\Product\IdPager;

class GetIdPagerByQuery {
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