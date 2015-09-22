<?php

namespace Controller\Search;

class Autocomplete {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $helper = new \Helper\TemplateHelper();
        $searchTerm = trim(mb_strtolower($request->query->get('q')));

        /** @var \Model\Product\Category\Entity[] $categories */
        $categories = [];
        /** @var \Model\Product\Entity[] $products */
        $products = [];

        if ($searchTerm) {
            \App::scmsClient()->addQuery('api/search/autocomplete', [
                'request' => $searchTerm,
                'geo_town_id' => \App::user()->getRegion()->getId(),
            ], [], function($data) use(&$categories, &$products) {
                if (isset($data['categories']) && is_array($data['categories'])) {
                    $categories = array_map(function($item) {
                        return new \Model\Product\Category\Entity($item);
                    }, array_slice($data['categories'], 0, 5));
                }

                if (isset($data['products']) && is_array($data['products'])) {
                    $products = array_map(function($item) {
                        return new \Model\Product\Entity($item);
                    }, array_slice($data['products'], 0, 5));
                }
            }, function($e)  {
                \App::exception()->remove($e);
                \App::logger()->error($e);
            });

            \App::scmsClient()->execute();
        }

        return new \Http\JsonResponse([
            'result' => [
                'categories' => array_values(array_map(function(\Model\Product\Category\Entity $category) use($helper) {
                    return [
                        'id'  => $category->id,
                        'image' => $category->getMediaSource('category_163x163')->url,
                        'link'  => $category->getLink(),
                        'name'  => $helper->unescape($category->getName()) . ' (' . $category->getProductCount() . ')',
                        'token'  => $category->getToken(),
                    ];
                }, $categories)),
                'products' => array_values(array_map(function(\Model\Product\Entity $product) {
                    return [
                        'image' => $product->getMainImageUrl('product_60'),
                        'link'  => $product->getLink(),
                        'name'  => $product->getName(),
                    ];
                }, $products)),
            ],
        ]);
    }
}