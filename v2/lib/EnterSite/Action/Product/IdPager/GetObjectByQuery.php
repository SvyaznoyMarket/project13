<?php

namespace EnterSite\Action\Product\IdPager;

use EnterSite\ConfigTrait;
use Enter\Curl\Query;
use EnterSite\Model\Product\IdPager;

class GetObjectByQuery {
    use \EnterSite\ConfigTrait;

    public function execute(Query $query) {
        $pager = null;

        $item = $query->getResult();
        if ($item) {
            $pager = new IdPager($item);
        }

        return $pager;
    }
}