<?php

namespace Enter\Site\Action\Product\Filter;

use Enter\Http\Request;
use Enter\Site\Model\Product\RequestFilter;

class GetRequestObjectListByHttpRequest {
    /**
     * @param Request $request
     * @return RequestFilter[]
     */
    public function execute(Request $request) {
        $filters = [];

        foreach ($request->query as $key => $value) {
            if (0 === strpos($key, 'f-')) {
                $parts = array_pad(explode('-', $key), 3, null);

                if (!isset($filters[$parts[1]])) {
                    $filters[$parts[1]] = new RequestFilter();
                }

                if (('from' == $parts[2]) || ('to' == $parts[2])) {
                    $filters[$parts[1]]->value[$parts[2]] = $value;
                } else {
                    $filters[$parts[1]]->value[] = $value;
                }
            } else if (0 === strpos($key, 'tag-')) {
                if (!isset($filters['tag'])) {
                    $filters['tag'] = new RequestFilter();
                }

                $filters['tag']->value[] = $value;
            }
        }

        return $filters;
    }
}