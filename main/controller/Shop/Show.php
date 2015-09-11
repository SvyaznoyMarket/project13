<?php

namespace Controller\Shop;

class Show {
    /**
     * @param string $shopToken
     * @return \Http\Response
     * @throws \Exception\AccessDeniedException
     * @throws \Exception\NotFoundException
     */
    public function execute($shopToken) {
        $client = \App::coreClientV2();
        $user = \App::user();

        /** @var $point \Model\Point\ScmsPoint */
        \App::scmsClient()->addQuery('api/point/get', ['slugs' => [$shopToken], 'full' => 1], [], function($data) use(&$point) {
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

        $client->execute();

        if (!$point) {
            throw new \Exception\NotFoundException('Магазин ' . $shopToken . ' не найдена');
        }

        if ($point->town->names->nominativus) {
            \App::scmsClient()->addQuery('api/word-inflect', ['names' => [$point->town->names->nominativus]], [], function($data) use(&$point) {
                if (isset($data[$point->town->names->nominativus])) {
                    $point->town->names = new \Model\Inflections($data[$point->town->names->nominativus]);
                }
            });
        }

        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        $client->execute();

//        if (in_array($point->getId(), [194])) {
//            \RepositoryManager::product()->prepareIteratorByFilter(
//                [
//                    ['shop', 1, [$point->getId()]],
//                    ['is_view_list', 1, [true]],
//                ],
//                [],
//                null,
//                null,
//                $point->getRegion(),
//                function($data) use (&$point) {
//                    $point->setProductCount(isset($data['count']) ? $data['count'] : null);
//                },
//                function(\Exception $e) {
//                    \App::exception()->remove($e);
//                }
//            );
//
//        }
//        \App::curl()->execute();

        $page = new \View\Shop\ShowPage();
        $page->setTitle($point->partner->name . ', ' . $point->name);
        $page->setParam('point', $point);

        return new \Http\Response($page->show());
    }
}