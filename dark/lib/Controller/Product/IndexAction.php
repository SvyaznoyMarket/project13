<?php

namespace Controller\Product;

class IndexAction {
    public function execute($productPath, \Http\Request $request) {
        $productToken = explode('/', $productPath);
        $productToken = end($productToken);

        $productRepository = \RepositoryManager::getProduct();
        $product = $productRepository->getEntityByToken($productToken);
        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар с токеном "%s" не найден.', $productToken));
        }

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'name' => 'Каталог товаров',
            'url'  => '/catalog/',//$this->generateUrl('productCatalog'),
        );

        foreach ($product->getCategory() as $category) {
            $breadcrumbs[] = array(
                'name' => $category->getName(),
                'url'  => $category->getLink(),
            );
        }
        $breadcrumbs[] = array(
            'name' => $product->getName(),
            'url'  => $product->getLink(),
        );

        if ($product->getConnectedProductsViewMode() == $product::DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE) {
            $showRelatedUpper = false;
        } else {
            $showRelatedUpper = true;
        }

        $accessoriesId =  array_slice($product->getAccessoryId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $relatedId = array_slice($product->getRelatedId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $partsId = array();

        foreach ($product->getKit() as $part) {
            $partsId[] = $part->getId();
        }

        $accessories = array_flip($accessoriesId);
        $related = array_flip($relatedId);
        $kit = array_flip($partsId);

        if ((bool)$accessoriesId || (bool)$relatedId || (bool)$partsId) {
            $products = $productRepository->getCollectionById(array_merge($accessoriesId, $relatedId, $partsId));

            foreach ($products as $item) {
                if (isset($accessories[$item->getId()])) $accessories[$item->getId()] = $item;
                if (isset($related[$item->getId()])) $related[$item->getId()] = $item;
                if (isset($kit[$item->getId()])) $kit[$item->getId()] = $item;
            }
        }

        $page = new \View\Product\IndexPage();
        $page->setParam('product', $product);
        $page->setParam('title', $product->getName());
        $page->setParam('breadcrumbs', $breadcrumbs);
        $page->setParam('showRelatedUpper', $showRelatedUpper);
        $page->setParam('showAccessoryUpper', !$showRelatedUpper);
        $page->setParam('accessories', $accessories);
        $page->setParam('related', $related);
        $page->setParam('kit', $kit);

        return new \Http\Response($page->show());
    }
}