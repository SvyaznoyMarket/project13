<?php

namespace Controller\Product;

use Http\Request;
use Http\JsonResponse;
use Model\Product\Entity as Product;

class ViewedAction
{
    public function execute(Request $request)
    {

        $products = array_map(
            function ($item) {
                return new Product([ 'id' => $item ]);
            },
            explode(',', $request->cookies->get('product_viewed', ''))
        );

        if (!$products || !$template = $request->query->get('template')) {
            return null;
        }

        $products = array_reverse($products);

        \RepositoryManager::product()->prepareProductQueries($products, 'media label category brand');

        \App::curl()->execute();

        \RepositoryManager::review()->addScores($products);

        \App::curl()->execute();

        $html = \App::templating()->render($template, [
            'products'  => $products
        ]);

        $result = [
            'result' => [
                'content'   => $html,
            ]
        ];

        return new JsonResponse($result);

    }
}
