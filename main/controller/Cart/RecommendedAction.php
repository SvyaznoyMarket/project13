<?php
namespace Controller\Cart;

class RecommendedAction {
    /**
     * Требуемые рекомендации передаются в get массиве types. Допустимые значения: alsoBought, popupar, personal
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $request->query->set('productIds', array_map(function(\Model\Cart\Product\Entity $product) { return $product->id; }, \App::user()->getCart()->getProductsById()));
        return (new \Controller\Recommended())->execute($request);
    }
}