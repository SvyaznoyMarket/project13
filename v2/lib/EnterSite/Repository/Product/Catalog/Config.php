<?php

namespace EnterSite\Repository\Product\Catalog;

use Enter\Curl\Query;
use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Model;

class Config {
    use ConfigTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @param Query $query
     * @return Model\Product\Catalog\Config|null
     */
    public function getObjectByQuery(Query $query) {
        $object = null;

        try {
            $item = $query->getResult();
            if ($item) {
                $object = new Model\Product\Catalog\Config($item);
            }
        } catch (\Exception $e) {
            $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['repository']]);
            trigger_error($e, E_USER_ERROR);
        }

        return $object;
    }

    public function getLimitByHttpRequest(Http\Request $request) {
        $limit = (int)$request->query['limit'];
        if (($limit >= 400) || ($limit <= 0)) {
            $limit = $this->getConfig()->product->itemPerPage;
        }

        return $limit;
    }
}