<?php

namespace EnterSite\Repository;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\LoggerTrait;
use EnterSite\Model;

class Promo {
    use LoggerTrait;

    /**
     * @param Query $query
     * @return Model\Promo[]
     */
    public function getObjectListByQuery(Query $query) {
        $promos = [];

        try {
            foreach ($query->getResult() as $item) {
                $promos[] = new Model\Promo($item);
            }
        } catch (\Exception $e) {
            $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['repository']]);
            //trigger_error($e, E_USER_ERROR);
        }

        return $promos;
    }
}