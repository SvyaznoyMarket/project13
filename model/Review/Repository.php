<?php

namespace Model\Review;

class Repository {

    CONST NUM_REVIEWS_ON_PAGE = 7;

    /** @var \ReviewsStore\Client */
    private $client;

    /**
     * @param \ReviewsStore\Client $client
     */
    public function __construct(\ReviewsStore\Client $client) {
        $this->client = $client;
    }

    /**
     * Получает информацию по отзывам для товара
     *
     * @param $product
     * @return array
     */
    public function getReviews($productId, $reviewsType = '', $currentPage = 0, $perPage = self::NUM_REVIEWS_ON_PAGE) {

        $client = clone $this->client;

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
        $client->execute(\App::config()->reviewsStore['retryTimeout']['medium']);

        return $result;
    }


    /**
     * Получает информацию по оценкам для группы товаров
     *
     * @param string $productIdList (строка из id, разделенных запятой)
     * @return array
     */
    public function getScores($productIdList) {
        if(!(bool)$productIdList) return [];

        $client = clone $this->client;
        $result = [];

        $client->addQuery('scores-list', [
                'product_list' => $productIdList,
            ], [], function($data) use(&$result) {
                $result = $data;
            },  function(\Exception $e) use (&$exception) {
                $exception = $e;
                \App::exception()->remove($e);
        });
        $client->execute(\App::config()->reviewsStore['retryTimeout']['medium']);

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
    public function addScoresGrouped(&$collections) {

        $productIds = [];
        foreach ($collections as $collectionData) {
            foreach ($collectionData['collection'] as $product) {
                $productIds[] = $product->getId();
            }
        }

        $scoresData = $this->getScores(implode(',', $productIds));

        if(empty($scoresData['product_scores'])) return $collections;

        $scoredIds = array_map(function($score){ return (int)$score['product_id']; }, $scoresData['product_scores']);

        foreach ($collections as $collectionData) {
            foreach ($collectionData['collection'] as $product) {
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

        return $collections;
    }


    /**
     * Подготавливает данные для отображения рейтингов отзывов
     *
     * @param $userData
     * @param $proData
     * @return array
     */
    public function prepareReviewsDataSummary($userData, $proData) {
        $summaryData = [
            'user' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
            'pro' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
        ];
        foreach (['user' => $userData, 'pro' => $proData] as $type => $data) {
            if(empty($data['num_users_by_score'])) continue;
            foreach ($data['num_users_by_score'] as $grade) {
                $score = (float)($grade['score']);
                if($score <= 2.0) {
                    $summaryData[$type][1] += $grade['count'];
                } elseif($score > 2.0 && $score <= 4.0) {
                    $summaryData[$type][2] += $grade['count'];
                } elseif($score > 4.0 && $score <= 6.0) {
                    $summaryData[$type][3] += $grade['count'];
                } elseif($score > 6.0 && $score <= 8.0) {
                    $summaryData[$type][4] += $grade['count'];
                } elseif($score > 8.0) {
                    $summaryData[$type][5] += $grade['count'];
                }
            }
        }
        return $summaryData;
    }

}