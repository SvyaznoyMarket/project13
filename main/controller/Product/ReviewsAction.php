<?php

namespace Controller\Product;

class ReviewsAction {

    CONST NUM_REVIEWS_ON_PAGE = 7;

    /**
     * @param \Http\Request $request
     * @param int $productId
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = $request->get('page', 0);
        $reviewsType = $request->get('type', 'user');

        $reviewsData = $this->getReviews($productId, $reviewsType, $page);

        $response = '';

        foreach ($reviewsData['review_list'] as $key => $review) {
            $response .= \App::templating()->render('product/_review', [
                'review' => $review,
                'last' => empty($reviewsData['review_list'][$key + 1])
            ]);
        }

        return new \Http\JsonResponse(['content' => $response, 'pageCount' => $reviewsData['page_count']]);
    }


    /**
     * Получает информацию по отзывам для товара
     *
     * @param $product
     * @return array
     */
    public function getReviews($productId, $reviewsType = 'user', $currentPage = 0, $perPage = self::NUM_REVIEWS_ON_PAGE) {

        $client = \App::reviewsClient();
        $result = [];
        $client->addQuery('list', [
                'product_id' => $productId,
                'current_page' => $currentPage,
                'page_size' => $perPage,
                'type' => $reviewsType,
            ], [], function($data) use(&$result) {
                $result = $data;
            },  function($data) use(&$result) {
                $result = $data;
        });
        $client->execute(\App::config()->corePrivate['retryTimeout']['medium']);

        return $result;
    }

}