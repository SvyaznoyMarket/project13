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

        return (new \Controller\Smartengine\Action())
            ->pullProductAlsoViewed($request, $productId, ['categoryClass' => 'jewel']);
    }
}