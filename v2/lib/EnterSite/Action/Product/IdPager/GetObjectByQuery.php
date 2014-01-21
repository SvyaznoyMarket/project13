<?php

namespace EnterSite\Action\Product\IdPager;

use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class GetObjectByQuery {
    use ConfigTrait;

    public function execute(Query $query) {
        $pager = null;

        $item = $query->getResult();
        if ($item) {
            $pager = new Model\Product\IdPager($item);
        }

        return $pager;
    }
}