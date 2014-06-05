<?php

namespace EnterSite\Controller;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Model\Page\Index as Page;

class Index {
    use ConfigTrait, LoggerTrait, CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, MustacheRendererTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }

    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();
        $productCategoryRepository = new Repository\Product\Category();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос категорий
        $categoryListQuery = new Query\Product\Category\GetTreeList($region->id, 3);
        $curl->prepare($categoryListQuery);

        $curl->execute();

        // категории
        $categories = $productCategoryRepository->getListByQuery($categoryListQuery);
        if (!(bool)$categories) {
            $e = new \Exception('На главной нет категорий');

            $this->getLogger()->push(['type' => 'critical', 'error' => $e, 'action' => __METHOD__, 'tag' => ['controller']]);
            trigger_error($e, E_USER_ERROR);
        }

        // запрос меню
        $mainMenuQuery = new Query\MainMenu\GetItem();
        $curl->prepare($mainMenuQuery);

        $curl->execute();

        // меню
        $mainMenu = (new Repository\MainMenu())->getObjectByQuery($mainMenuQuery, $categoryListQuery);

        // запрос для получения страницы
        $pageRequest = new Repository\Page\Index\Request();
        $pageRequest->region = $region;
        $pageRequest->mainMenu = $mainMenu;
        $pageRequest->categories = $categories;
        //die(json_encode($pageRequest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // страница
        $page = new Page();
        (new Repository\Page\Index())->buildObjectByRequest($page, $pageRequest);
        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // рендер
        $renderer = $this->getRenderer();
        $renderer->setPartials([
            'content' => 'page/main/content',
        ]);
        $content = $renderer->render('layout/default', $page);

        // http-ответ
        $response = new Http\Response($content);

        return $response;
    }
}