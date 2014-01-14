<?php

namespace Controller\Product;

class RecommendedAction {
    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);
    }
}