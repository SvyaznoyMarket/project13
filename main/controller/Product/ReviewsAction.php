<?php

namespace Controller\Product;

class ReviewsAction {

    /**
     * @param \Http\Request $request
     * @param int $productId
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = $request->get('page', 0);
        $reviewsType = $request->get('type', 'user');

        $reviewsData = \RepositoryManager::review()->getReviews($productId, $reviewsType, $page);

        $response = '';

        if(!empty($reviewsData['review_list'])) {
            foreach ($reviewsData['review_list'] as $key => $review) {
                $response .= \App::templating()->render('product/_review', [
                    'page' => (new \View\Product\IndexPage()),
                    'review' => $review,
                    'last' => empty($reviewsData['review_list'][$key + 1])
                ]);
            }
        }

        return new \Http\JsonResponse(['content' => $response, 'pageCount' => $reviewsData['page_count']]);
    }

}