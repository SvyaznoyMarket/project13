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
        $logger = $this->getLogger();
        $curl = $this->getCurlClient();

        $regionRepository = new Repository\Region();

        $regionId = $regionRepository->getIdByHttpRequestQuery($request);
        $keyword = trim((string)$request->query['q']);

        // response
        $response = (new Controller\Redirect())->execute($request->server['HTTP_REFERER'] ?: $this->getRouter()->getUrlByRoute(new Routing\Index()), 302);

        if (!$regionId && (mb_strlen($keyword) >= 3)) {
            $regionListQuery = new Query\Region\GetListByKeyword($keyword);
            $curl->prepare($regionListQuery)->execute();

            $regionData = $regionListQuery->getResult();
            $regionId = isset($regionData[0]['id']) ? (string)$regionData[0]['id'] : null;
        }
        if (!$regionId) {
            $e = new \Exception('Не указан ид региона');
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['region']]);

            return $response;
        }

        // запрос региона
        $regionItemQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionItemQuery)->execute();

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