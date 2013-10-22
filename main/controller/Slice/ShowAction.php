<?php

namespace Controller\Slice;

class ShowAction {
    public function execute($sliceToken, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        /** @var $slice \Model\Slice\Entity|null */
        $slice = null;
        \RepositoryManager::slice()->prepareEntityByToken($sliceToken, function($data) use (&$slice) {
            if (is_array($data) && (bool)$data) {
                $slice = new \Model\Slice\Entity($data);
            }
        });
        \App::dataStoreClient()->execute();

        if (!$slice) {
            throw new \Exception\NotFoundException(sprintf('Срез @%s не найден', $sliceToken));
        }

        // добывание фильтров из среза
        $requestData = [];
        parse_str($slice->getFilterQuery(), $requestData);

        $values = [];
        foreach ($requestData as $k => $v) {
            if (0 !== strpos($k, \View\Product\FilterForm::$name)) continue;
            $parts = array_pad(explode('-', $k), 3, null);

            if (!isset($values[$parts[1]])) {
                $values[$parts[1]] = [];
            }
            if (('from' == $parts[2]) || ('to' == $parts[2])) {
                $values[$parts[1]][$parts[2]] = $v;
            } else {
                $values[$parts[1]][] = $v;
            }
        }

        $filterData = []; // https://wiki.enter.ru/pages/viewpage.action?pageId=20448554#id-%D0%92%D0%BD%D0%B5%D1%88%D0%BD%D0%B8%D0%B9%D0%B8%D0%BD%D1%82%D0%B5%D1%80%D1%84%D0%B5%D0%B9%D1%81-%D0%A4%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81%D0%BE%D0%B2:
        foreach ($values as $k => $v) {
            if (isset($v['from']) || isset($v['to'])) {
                $filterData[] = [$k, 2, isset($v['from']) ? $v['from'] : null, isset($v['to']) ? $v['to'] : null];
            } else {
                $filterData[] = [$k, 1, $v];
            }
        }
        die(var_dump($filterData));

        die(var_dump($slice));

        $page = new \View\Slice\ShowPage();
        $page->setParam('slice', $slice);

        return new \Http\Response($page->show());
    }
}