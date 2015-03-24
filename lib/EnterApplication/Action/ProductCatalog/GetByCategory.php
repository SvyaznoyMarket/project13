<?php

namespace EnterApplication\Action\ProductCatalog
{
    use EnterApplication\Action\ProductCatalog\GetByCategory\Request;
    use EnterApplication\Action\ProductCatalog\GetByCategory\Response;
    use EnterQuery as Query;

    class GetByCategory {
        use \EnterApplication\CurlTrait;
        use \EnterApplication\Action\ActionTrait;

        /**
         * @param Request $request
         * @return Response
         */
        public function execute(Request $request)
        {
            //$startAt = microtime(true);
            //$GLOBALS['startAt'] = $startAt;

            $curl = $this->getCurl();

            // регион
            $regionQuery = $this->getRegionQuery($request->regionId);

            // редирект
            $redirectQuery = (new Query\Redirect\GetByUrl($request->urlPath))->prepare(); // TODO: throw Exception

            // аб-тест
            $abTestQuery = (new Query\AbTest\GetActive())->prepare();

            // главное меню
            $menuQuery = (new Query\MainMenu\GetByTagList(['site-web']))->prepare();

            // выполнение запросов
            $curl->execute();

            // проверка региона
            $this->checkRegionQuery($regionQuery);

            // выполнение запросов
            $curl->execute();

            $this->removeCurl();

            // обработка ошибок
            if ($menuQuery->error) {
                $menuQuery->response->items = \App::dataStoreClient()->query('/main-menu.json')['item'];

                \App::logger()->error(['error' => $menuQuery->error, 'sender' => __FILE__ . ' ' .  __LINE__], ['main_menu', 'controller']);
            }

            // response
            $response = new Response();
            $response->redirectQuery = $redirectQuery;
            $response->abTestQuery = $abTestQuery;
            $response->regionQuery = $regionQuery;

            return $response;
        }

        /**
         * @return Request
         */
        public function createRequest()
        {
            return new Request();
        }
    }
}

namespace EnterApplication\Action\ProductCatalog\GetByCategory
{
    use EnterQuery as Query;

    class Request
    {
        /** @var string */
        public $urlPath;
        /** @var string */
        public $regionId;
        /** @var string|null */
        public $userToken;
        /** @var array */
        public $categoryCriteria;
    }

    class Response
    {
        /** @var Query\Redirect\GetByUrl */
        public $redirectQuery;
        /** @var Query\AbTest\GetActive */
        public $abTestQuery;
        /** @var Query\Region\GetById */
        public $regionQuery;
    }
}