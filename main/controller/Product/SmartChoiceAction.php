<?php


namespace Controller\Product;


class SmartChoiceAction {

    /**
     * @param array $productIds
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productIds) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $rrConfig = \App::config()->partners['RetailRocket'];
        $region = \App::user()->getRegion();
        $contoller = new \Controller\Product\SimilarAction();
        $responce = '';



        return new \Http\JsonResponse(array('result' => $responce));

    }

} 