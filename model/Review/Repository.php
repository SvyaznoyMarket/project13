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

    public function prepareData($productUi, $currentPage = 0, $perPage = self::NUM_REVIEWS_ON_PAGE, $done) {
        $this->client->addQuery(
            'list',
            [
                'product_ui'   => $productUi,
                'current_page' => $currentPage,
                'page_size'    => $perPage,
            ],
            [],
            $done,
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );
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
                'product_ui' => $productIdList,
            ], [], function($data) use(&$result) {
                $result = $data;
            },  function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );
        $client->execute(\App::config()->reviewsStore['retryTimeout']['medium']);

        return is_array($result) ? $result : [];
    }

    /**
     * @param \Model\Product\Entity[] $products
     * @param \Closure $done
     */
    public function prepareScoreCollection(array $products, $done) {
        if (!$products) {
            return;
        }

        /** @var \Model\Product\Entity $firstProduct */
        $firstProduct = reset($products);

        if ($firstProduct->id) {
            $params = [
                'product_list' => implode(',', array_map(function(\Model\Product\Entity $product) { return $product->id; }, $products)),
            ];
        } else {
            $params = [
                'product_ui' => implode(',', array_map(function(\Model\Product\Entity $product) { return $product->ui; }, $products)),
            ];
        }

        $this->client->addQuery(
            'scores-list',
            $params,
            [],
            $done,
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );
    }

    /**
     * Устанавливает рейтинги для коллекции товаров
     *
     * @param \Model\Product\Entity[] $products
     * @return \Model\Product\Entity[] $products
     */
    public function addScores(&$products, &$scoreData = null) {
        if (null === $scoreData) {
            $scoreData = \App::config()->product['reviewEnabled'] ? $this->getScores(implode(',', array_map(function($product){ return $product->getUi(); }, $products))) : [];
        }

        if (empty($scoreData['product_scores'])) {
            return;
        }

        $scoredUis = array_map(function($score){ return $score['product_ui']; }, $scoreData['product_scores']);

        foreach ($products as $product) {
            if(in_array($product->getUi(), $scoredUis)) {
                $productScore = null;
                foreach ($scoreData['product_scores'] as $key => $score) {
                    if($score['product_ui'] == $product->getUi()) {
                        $productScore = $score;
                        unset($scoreData['product_scores'][$key]);
                    }
                }
                if(!$productScore) continue;
                if(!empty($productScore['score'])) $product->setAvgScore($productScore['score']);
                if(!empty($productScore['star_score'])) $product->setAvgStarScore($productScore['star_score']);
                if(!empty($productScore['num_reviews'])) $product->setNumReviews($productScore['num_reviews']);
            }
        }
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
     * @return array
     */
    public function getReviewsDataSummary($userData) {
        $summaryData = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        if(!empty($userData['num_users_by_score'])) {
            foreach ($userData['num_users_by_score'] as $grade) {
                $score = (float)($grade['score']);
                if($score <= 2.0) {
                    $summaryData[1] += $grade['count'];
                } elseif($score > 2.0 && $score <= 4.0) {
                    $summaryData[2] += $grade['count'];
                } elseif($score > 4.0 && $score <= 6.0) {
                    $summaryData[3] += $grade['count'];
                } elseif($score > 6.0 && $score <= 8.0) {
                    $summaryData[4] += $grade['count'];
                } elseif($score > 8.0) {
                    $summaryData[5] += $grade['count'];
                }
            }
        }

        return $summaryData;
    }

}