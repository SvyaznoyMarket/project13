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

        $responseData = [];

        try {
            $recommend = [
                'alsoBought' => null,
                'similar'    => null,
                'alsoViewed' => null
            ];

            foreach ($recommend as $type => $item) {
                switch ($type) {
                    case 'alsoBought':
                        $recommend[$type] = (new \Controller\Product\UpsaleAction())->execute($productId, $request, false);
                        break;
                    case 'similar':
                        $recommend[$type] = (new \Controller\Product\SimilarAction())->execute($productId, $request, false);
                        break;
                    case 'alsoViewed':
                        $recommend[$type] = (new \Controller\Product\AlsoViewedAction())->execute($productId, $request, false);
                        break;
                }
            }

            $responseData = [
                'success' => true,
                'recommend' => $recommend
            ];
        } catch (\Exception $e) {
            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}