<?php

namespace Controller\Shop;

class Show {
    /**
     * @param string $pointToken
     * @return \Http\Response
     * @throws \Exception\AccessDeniedException
     * @throws \Exception\NotFoundException
     */
    public function execute($pointToken) {
        $scmsClient = \App::scmsClient();
        $helper = new \Templating\Helper();
        $user = \App::user();
        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $sidebarHtml = '';

        /** @var \Model\Point\ScmsPoint|null $point */
        $point = null;
        $scmsClient->addQuery('api/point/get', ['slugs' => [$pointToken], 'full' => 1], [], function($data) use(&$point) {
            if (isset($data['points']) && isset($data['points'][0])) {
                $point = new \Model\Point\ScmsPoint($data['points'][0]);
                
                if (isset($data['partners'])) {
                    foreach ($data['partners'] as $partner) {
                        if ($partner['slug'] === $point->partner->slug) {
                            $point->partner = new \Model\Point\Partner($partner);
                        }
                    }
                }
            }
        });

        $scmsClient->addQuery(
            'api/static-page',
            [
                'token' => ['menu'],
                'geo_town_id' => \App::user()->getRegion()->id,
                'tags' => ['site-web'],
            ],
            [],
            function($data) use (&$sidebarHtml) {
                if (isset($data['pages'][0]['content'])) {
                    $sidebarHtml = (string)$data['pages'][0]['content'];
                }
            }
        );

        $scmsClient->execute();

        if (!$point) {
            throw new \Exception\NotFoundException('Точка ' . $pointToken . ' не найдена');
        }

        // Получаем названия партнёров точек самовывоза в разных падежах
        $scmsClient->addQuery('api/word-inflect', ['names' => [$point->partner->names->nominativus/*, $point->town->names->nominativus*/]], [], function($data) use(&$point) {
//            if (isset($data[$point->town->names->nominativus])) {
//                $point->town->names = new \Model\Inflections($data[$point->town->names->nominativus]);
//            }

            if (isset($data[$point->partner->names->nominativus])) {
                $point->partner->names = new \Model\Inflections($data[$point->partner->names->nominativus]);
            }
        });

        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        if ($point->id) {
            \App::searchClient()->addQuery('v2/listing/list', [
                'filter' => [
                    'filters' => [
                        ['is_model', 1, 1],
                        ['is_view_list', 1, 1],
                        ['shop', 1, $point->id],
                    ],
                    'limit' => 50,
                ],
                'region_id' => \App::user()->getRegion()->getId(),
            ], [], function($response) use(&$products) {
                if (!isset($response['list'][0])) {
                    return;
                }

                foreach (array_rand($response['list'], 4) as $key) {
                    $products[] = new \Model\Product\Entity(['id' => $response['list'][$key]]);
                }
            });
        }

        \App::curl()->execute();
        \RepositoryManager::product()->prepareProductQueries($products, 'media label brand category');
        \App::curl()->execute();

        $page = new \View\Shop\ShowPage();
        $page->setTitle($helper->ucfirst($point->partner->names->nominativus) . ', ' . $point->name);
        $page->setParam('title', $page->getTitle());
        $page->setParam('sidebarHtml', $sidebarHtml);
        $page->setParam('point', $point);
        $page->setParam('products', $products);

        return new \Http\Response($page->show());
    }
}