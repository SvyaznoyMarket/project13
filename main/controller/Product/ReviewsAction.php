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


    /**
     * Получает информацию по отзывам для товара
     *
     * @param $product
     * @return array
     */
    public function getReviews($productId, $reviewsType = '', $currentPage = 0, $perPage = self::NUM_REVIEWS_ON_PAGE) {

        $client = \App::reviewsClient();
        $result = [];
        $client->addQuery('list', [
                'product_id' => $productId,
                'current_page' => $currentPage,
                'page_size' => $perPage,
                'type' => $reviewsType,
            ], [], function($data) use(&$result) {
                $result = $data;
            },  function(\Exception $e) use (&$exception) {
                $exception = $e;
                \App::exception()->remove($e);
        });
        $client->execute(\App::config()->corePrivate['retryTimeout']['medium']);

        return $result;
    }


    /**
     * Получает информацию по оценкам для группы товаров
     *
     * @param string $productIdList (строка из id, разделенных запятой)
     * @return array
     */
    public function getScores($productIdList) {

        $client = \App::reviewsClient();
        $result = [];

        $client->addQuery('scores-list', [
                'product_list' => $productIdList,
            ], [], function($data) use(&$result) {
                $result = $data;
            },  function(\Exception $e) use (&$exception) {
                $exception = $e;
                \App::exception()->remove($e);
        });
        $client->execute(\App::config()->corePrivate['retryTimeout']['medium']);

        return is_array($result) ? $result : [];
    }


    /**
     * Устанавливает коллекции товаров рейтинги
     *
     * @param array $products
     * @return array $products
     */
    public function addScores(&$products) {

        $scoresData = $this->getScores(implode(',', array_map(function($product){ return $product->getId(); }, $products)));

        if(empty($scoresData['product_scores'])) return $products;

        $scoredIds = array_map(function($score){ return (int)$score['product_id']; }, $scoresData['product_scores']);

        foreach ($products as $product) {
            if(in_array($product->getId(), $scoredIds)) {
                $productScore = null;
                foreach ($scoresData['product_scores'] as $key => $score) {
                    if($score['product_id'] == $product->getId()) {
                        $productScore = $score;
                        unset($scoresData['product_scores'][$key]);
                    }
                }
                if(!$productScore) continue;
                if(!empty($productScore['score'])) $product->setAvgScore($productScore['score']);
                if(!empty($productScore['star_score'])) $product->setAvgStarScore($productScore['star_score']);
                if(!empty($productScore['num_reviews'])) $product->setNumReviews($productScore['num_reviews']);
            }
        }

        return $products;
    }


    /**
     * Устанавливает коллекции коллекций товаров рейтинги
     *
     * @param array $products
     * @return array $products
     */
    public function addScoresGrouped(&$collection) {

        $productIds = [];
        foreach ($collection as $products) {
            foreach ($products as $product) {
                $productIds[] = $product->getId();
            }
        }

        $scoresData = $this->getScores(implode(',', $productIds));

        if(empty($scoresData['product_scores'])) return $collection;

        $scoredIds = array_map(function($score){ return (int)$score['product_id']; }, $scoresData['product_scores']);

        foreach ($collection as $products) {
            foreach ($products as $product) {
                if(in_array($product->getId(), $scoredIds)) {
                    $productScore = null;
                    foreach ($scoresData['product_scores'] as $key => $score) {
                        if($score['product_id'] == $product->getId()) {
                            $productScore = $score;
                            unset($scoresData['product_scores'][$key]);
                        }
                    }
                    if(!$productScore) continue;
                    if(!empty($productScore['score'])) $product->setAvgScore($productScore['score']);
                    if(!empty($productScore['star_score'])) $product->setAvgStarScore($productScore['star_score']);
                    if(!empty($productScore['num_reviews'])) $product->setNumReviews($productScore['num_reviews']);
                }
            }
        }

        return $collection;
    }

}