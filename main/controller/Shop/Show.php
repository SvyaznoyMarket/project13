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

        /** @var $point \Model\Point\ScmsPoint */
        $scmsClient->addQuery('api/point/get', ['slugs' => [$pointToken], 'full' => 1], [], function($data) use(&$point) {
            if (isset($data['points']) && is_array($data['points'])) {
                $point = new \Model\Point\ScmsPoint($data['points'][0]);
            }

            if (isset($data['partners'])) {
                foreach ($data['partners'] as $partner) {
                    if ($partner['slug'] === $point->partner->slug) {
                        $point->partner = new \Model\Point\Partner($partner);
                    }
                }
            }
        });

        $scmsClient->addQuery(
            'api/static-page',
            ['token' => ['menu']],
            [],
            function($data) use (&$sidebar) {
                if (isset($data['pages'][0]['content'])) {
                    $sidebar = (string)$data['pages'][0]['content'];
                }
            }
        );

        $scmsClient->execute();

        if (!$point) {
            throw new \Exception\NotFoundException('Магазин ' . $pointToken . ' не найдена');
        }

        $scmsClient->addQuery('api/word-inflect', ['names' => [$point->partner->names->nominativus, $point->town->names->nominativus]], [], function($data) use(&$point) {
            if (isset($data[$point->town->names->nominativus])) {
                $point->town->names = new \Model\Inflections($data[$point->town->names->nominativus]);
            }

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

        if (in_array($point->id, [194])) {
            \RepositoryManager::product()->prepareIteratorByFilter(
                [
                    ['shop', 1, [$point->id]],
                    ['is_view_list', 1, [true]],
                ],
                [],
                null,
                null,
                null,
                function($data) use (&$point) {
                    if (isset($data['count'])) {
                        $point->productCount = (int)$data['count'];
                    }
                }
            );
        }

        \App::curl()->execute();

        $page = new \View\Shop\ShowPage();
        $page->setTitle($helper->ucfirst($point->partner->names->nominativus) . ', ' . $point->name);
        $page->setParam('title', $page->getTitle());
        $page->setParam('sidebar', $sidebar);
        $page->setParam('point', $point);

        return new \Http\Response($page->show());
    }
}