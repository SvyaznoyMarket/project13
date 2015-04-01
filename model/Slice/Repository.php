<?php

namespace Model\Slice;

class Repository {
    /** @var \Scms\Client */
    private $client;

    public function __construct(\Scms\Client $client) {
        $this->client = $client;
    }

    /**
     * @param $token
     * @param $done
     * @param $fail
     */
    public function prepareEntityByToken($token, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('get-slice', ['url' => $token], [], $done, $fail);
    }

    /**
     * Получение фильтров среза
     * @return array
     */
    public function getSliceFiltersForSearchClientRequest(\Model\Slice\Entity $slice, $excludeCategory = false) {
        $sliceRequestFilters = [];
        parse_str($slice->getFilterQuery(), $sliceRequestFilters);

        $values = [];
        foreach ($sliceRequestFilters as $k => $v) {
            if ('q' === $k) {
                $values['text'] = $v;
            } else if ('category' == $k) {
                if (!$excludeCategory) {
                    $values['category'] = $v;
                }
            } elseif (0 === strpos($k, \View\Product\FilterForm::$name)) {
                $parts = array_pad(explode('-', $k), 3, null);

                if (!isset($values[$parts[1]])) {
                    $values[$parts[1]] = [];
                }
                if (('from' == $parts[2]) || ('to' == $parts[2])) {
                    $values[$parts[1]][$parts[2]] = $v;
                } else {
                    $values[$parts[1]][] = $v;
                }
            } elseif (0 === strpos($k, 'tag-')) {
                // добавляем теги в фильтр
                if (isset($values['tag'])) {
                    $values['tag'][] = $v;
                } else {
                    $values['tag'] = [$v];
                }
            }
        }

        $filters = [];
        foreach ($values as $k => $v) {
            if ('f-segment' == $k) {
                $filters[] = ['segment', 4, $v];
            } else if ('text' === $k) {
                $filters[] = [$k, 3, $v];
            } elseif (isset($v['from']) || isset($v['to'])) {
                $filters[] = [$k, 2, isset($v['from']) ? $v['from'] : null, isset($v['to']) ? $v['to'] : null];
            } else {
                $filters[] = [$k, 1, $v];
            }
        }

        return $filters;
    }
}