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

            // бренд
            $brandQuery = null;
            if (!empty($request->brandCriteria['token'])) {
                $brandQuery = (new Query\Brand\GetByToken($request->brandCriteria['token'], $regionQuery->response->region['id']))->prepare();
            }

            // категория
            $categoryQuery = (new Query\Product\Category\GetByToken(
                $request->categoryCriteria['token'],
                $regionQuery->response->region['id'],
                isset($request->brandCriteria['token']) ? $request->brandCriteria['token'] : null
            ))->prepare();

            // дерево категорий для меню
            //$categoryTreeQuery = (new Query\Product\Category\GetTree(null, 3, null, null, true))->prepare($categoryTreeError);
            $categoryRootTreeQuery = (new Query\Product\Category\GetRootTree($regionQuery->response->region['id'], 3))->prepare();

            // пользователь и его подписки
            $userQuery = null;
            $subscribeQuery = null;
            if ($request->userToken) {
                $userQuery = (new Query\User\GetByToken($request->userToken))->prepare();
                $subscribeQuery = (new Query\Subscribe\GetByUserToken($request->userToken))->prepare();
            }

            // список регионов для выбора города
            $mainRegionQuery = (new Query\Region\GetMain())->prepare();

            // каналы подписок
            $subscribeChannelQuery = (new Query\Subscribe\Channel\Get())->prepare();

            // выполнение запросов
            $curl->execute();

            /** @var Query\Product\Filter\Get $filterQuery */
            $filterQuery = null;
            call_user_func(function() use (&$categoryQuery, &$filterQuery, &$regionQuery) {
                $categoryId = $categoryQuery->response->category['id'];
                if (!$categoryId) return;

                if (1 === $categoryQuery->response->category['level']) return;

                $filterData = [
                    ['category', 1, $categoryId],
                ];

                $filterQuery = (new Query\Product\Filter\Get($filterData, $regionQuery->response->region['id']))->prepare();
            });

            // выполнение запросов
            $curl->execute();

            call_user_func(function() use (&$filterQuery, &$brandQuery, &$regionQuery) {
                if (!$filterQuery) return;

                foreach ($filterQuery->response->filters as $item) {
                    $id = isset($item['filter_id']) ? $item['filter_id'] : null;
                    if ('brand' === $id) {
                        usort($item['options'], function($a, $b) { return $b['quantity'] - $a['quantity']; });
                        $brandIds = array_column($item['options'], 'id');
                        $brandIds = array_slice($brandIds, 0, 60);

                        if ($brandIds) {
                            $brandQuery = (new Query\Brand\GetByIdList($brandIds, $regionQuery->response->region['id']))->prepare();
                        }

                        break;
                    }
                }
            });

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
            $response->categoryQuery = $categoryQuery;
            $response->brandQuery = $brandQuery;
            $response->mainRegionQuery = $mainRegionQuery;
            $response->subscribeChannelQuery = $subscribeChannelQuery;
            $response->categoryRootTreeQuery = $categoryRootTreeQuery;
            $response->menuQuery = $menuQuery;

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
        /** @var array|null */
        public $categoryCriteria;
        /** @var array|null */
        public $brandCriteria;
    }

    class Response
    {
        /** @var Query\User\GetByToken|null */
        public $userQuery;
        /** @var Query\Subscribe\GetByUserToken|null */
        public $subscribeQuery;
        /** @var Query\Redirect\GetByUrl */
        public $redirectQuery;
        /** @var Query\AbTest\GetActive */
        public $abTestQuery;
        /** @var Query\Region\GetById */
        public $regionQuery;
        /** @var Query\Product\Category\GetByToken */
        public $categoryQuery;
        /** @var Query\Brand\GetByToken|null */
        public $brandQuery;
        /** @var Query\Product\Filter\Get */
        public $filterQuery;
        /** @var Query\Region\GetMain */
        public $mainRegionQuery; // TODO: убрать, будет через ajax
        /** @var Query\Subscribe\Channel\Get */
        public $subscribeChannelQuery;
        /** @var Query\Product\Category\GetRootTree */
        public $categoryRootTreeQuery;
        /** @var Query\MainMenu\GetByTagList */
        public $menuQuery;
    }
}