<?php

namespace EnterSite\Controller\Region;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\CurlClientTrait;
use EnterSite\Curl\Query;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Controller;

class Set {
    use ConfigTrait, LoggerTrait, RouterTrait, CurlClientTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }

    /**
     * @param Http\Request $request
     * @throws \Exception
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $router = $this->getRouter();

        $regionRepository = new Repository\Region();

        $regionId = $regionRepository->getIdByHttpRequestQuery($request);
        if (!$regionId) {
            $keyword = trim((string)$request->query['q']);

            $regionListQuery = new Query\Region\GetListByKeyword($keyword);
            $curl->prepare($regionListQuery)->execute();

            $regionData = $regionListQuery->getResult();
            $regionId = isset($regionData[0]['id']) ? (string)$regionData[0]['id'] : null;
        }
        if (!$regionId) {
            throw new \Exception('Не указан ид региона');
        }

        // запрос региона
        $regionItemQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionItemQuery);

        $curl->execute();

        // response
        $response = (new Controller\Redirect())->execute($request->server['HTTP_REFERER'] ?: $this->getRouter()->getUrlByRoute(new Routing\Index()), 302);

        // регион
        $region = $regionRepository->getObjectByQuery($regionItemQuery);
        if ($region) {
            $cookie = new Http\Cookie(
                $config->region->cookieName,
                $region->id,
                time() + $config->session->cookieLifetime,
                '/',
                $config->session->cookieDomain,
                false,
                false
            );
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}