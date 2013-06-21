<?php

namespace Controller\Jewel\Product;

class RecommendedAction {
    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->crossss['enabled']) {
            (new \Controller\Crossss\ProductAction())->recommended($request, $productId);
        }

        return (new \Controller\Smartengine\Action())
            ->pullProductAlsoViewed($request, $productId, ['categoryClass' => 'jewel']);
    }
}