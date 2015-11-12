<?php

namespace controller\Main;

class CategoryBlock {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $client = \App::coreClientV2();

        $regionId = $request->query->get('regionId');

        /** @var \Model\Product\Category\Entity[] $categoriesByUi */
        $categoriesByUi = [
            '78bcec47-e1c0-4798-9cf7-1de705b348f6' => null,
            '9f47c28e-4a2a-470b-b90c-6e34d5fd311c' => null,
            'fb0b080f-11ad-495c-b684-e80ba0104237' => null,
            'abd31da8-37ba-4335-a78b-7f0d8fbb1f25' => null,
            'b9f11b13-6aae-4f1f-847b-e6c48334638b' => null,
            '7f5accb9-3d3f-495c-8a5f-40b26db31a0a' => null,
            '8c648846-e82b-4419-9a9c-777983d3a486' => null,
        ];
        \App::scmsClient()->addQuery('category/gets', [
            'uids'   => array_keys($categoriesByUi),
            'geo_id' => $regionId,
        ], [], function($data) use(&$categoriesByUi) {
            if (isset($data['categories']) && is_array($data['categories'])) {
                foreach ($data['categories'] as $item) {
                    $category = new \Model\Product\Category\Entity($item);
                    $categoriesByUi[$category->ui] = $category;
                }
            }
        });

        $client->execute();

        $content = \App::mustache()->render('main/infoBox', [
            'categories' => array_values(array_map(function (\Model\Product\Category\Entity $category) {
                return [
                    'name' => $category->name,
                    'url' => $category->getLink(),
                    'image' => [
                        'url' => $category->getMediaSource('category_163x163')->url,
                    ],
                ];
            }, $categoriesByUi))
        ]);;

        return new \Http\Response($content);
    }
} 