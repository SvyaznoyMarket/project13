<?php

namespace EnterSite\Repository\Product;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Model;

class Filter {
    use ConfigTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @param Http\Request $request
     * @return Model\Product\RequestFilter[]
     */
    public function getRequestObjectListByHttpRequest(Http\Request $request) {
        $filters = [];

        foreach ($request->query as $key => $value) {
            if (0 === strpos($key, 'f-')) {
                $parts = array_pad(explode('-', $key), 3, null);

                if (!isset($filters[$parts[1]])) {
                    $filters[$parts[1]] = new Model\Product\RequestFilter();
                }

                if (('from' == $parts[2]) || ('to' == $parts[2])) {
                    $filters[$parts[1]]->value[$parts[2]] = $value;
                } else {
                    $filters[$parts[1]]->value[] = $value;
                }
            } else if (0 === strpos($key, 'tag-')) {
                if (!isset($filters['tag'])) {
                    $filters['tag'] = new Model\Product\RequestFilter();
                }

                $filters['tag']->value[] = $value;
            }
        }

        return $filters;
    }

    /**
     * @param Query $query
     * @return Model\Product\Filter[]
     */
    public function getObjectListByQuery(Query $query) {
        $reviews = [];

        try {
            foreach ($query->getResult() as $item) {
                $reviews[] = new Model\Product\Filter($item);
            }
        } catch (\Exception $e) {
            $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['repository']]);

            trigger_error($e, E_USER_ERROR);
        }

        return $reviews;
    }
}