<?php

namespace EnterSite\Controller\Region;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\CurlClientTrait;
use EnterSite\Curl\Query;
use EnterSite\Routing;

class Autocomplete {
    use ConfigTrait, LoggerTrait, RouterTrait, CurlClientTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $curl = $this->getCurlClient();
        $router = $this->getRouter();

        $result = [];

        $keyword = trim((string)$request->query['q']);

        $regionListQuery = new Query\Region\GetListByKeyword($keyword);
        $curl->prepare($regionListQuery)->execute();

        $i = 0;
        foreach ($regionListQuery->getResult() as $regionItem) {
            if ($i >= 20) break;

            $result[] = [
                'name'  => $regionItem['name'] . ((!empty($regionItem['region']['name']) && ($regionItem['name'] != $regionItem['region']['name'])) ? (" ({$regionItem['region']['name']})") : ''),
                'url'   => $router->getUrlByRoute(new Routing\Region\SetById($regionItem['id'])),
            ];

            $i++;
        }

        return new Http\JsonResponse([
            'result' => $result,
        ]);
    }
}