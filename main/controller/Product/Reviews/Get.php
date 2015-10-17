<?php

namespace Controller\Product\Reviews;

class Get {

    /**
     * @param \Http\Request $request
     * @param string $productUi
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productUi) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $page = $request->get('page', 0);

        // сортировка
        $sorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $sorting->setActive($sortingName, $sortingDirection);

        $reviewsData = [];
        \RepositoryManager::review()->prepareData($productUi, $page, \Model\Review\Repository::NUM_REVIEWS_ON_PAGE, $sorting, function($data) use(&$reviewsData) {
            $reviewsData = (array)$data;
        });
        \App::curl()->execute();

        if (isset($reviewsData['review_list']) && is_array($reviewsData['review_list'])) {
            $response = '';
            foreach ($reviewsData['review_list'] as $key => $review) {
                $response .= \App::templating()->render('product/_review', [
                    'page' => (new \View\Product\IndexPage()),
                    'review' => $review,
                    'last' => empty($reviewsData['review_list'][$key + 1]),
                ]);
            }
        } else {
            $response = 'Нет отзывов';
        }

        return new \Http\JsonResponse(['content' => $response, 'pageCount' => empty($reviewsData['page_count']) ? 0 : $reviewsData['page_count']]);
    }
}