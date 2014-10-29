<?php


namespace controller\Cart;


class RecommendedAction {

    public function execute(\Http\Request $request) {

        $cart = \App::user()->getCart();
        $recommendedProducts = [];
        $recommendController = new \Controller\Product\BasicRecommendedAction();

        /* Для всех продуктов корзины получим рекомендации */
        /* Неплохо распараллелить запросы, ну да ладно */
        foreach ($cart->getProducts() as $product) {
            $recommendedProducts = array_merge($recommendedProducts, $recommendController->getProductsIdsFromRetailrocket($product, $request, 'CrossSellItemToItems'));
        }

        /* Перемешаем и обрежем массив продуктов */
        shuffle($recommendedProducts);
        $recommendedProducts = array_slice($recommendedProducts, 0, 20);

        /* Получаем продукты из ядра */
        $products = \RepositoryManager::product()->getCollectionById($recommendedProducts);

        /* Рендерим слайдер */
        $slider = \App::closureTemplating()->render('product/__slider', [
            'products'  => $products,
            'class'     => 'bSlider-7item',
        ]);

        $recommend = [];
        $recommend['alsoBought'] = [
            'content'   => $slider,
            'success'   => true
            ];

        return new \Http\JsonResponse(['success'=> true, 'recommend' => $recommend]);
    }

} 