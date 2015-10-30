<?php

namespace View\Product;


class ReviewCompact2Action
{
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product
    ) {

        $score = $product->getAvgStarScore();

        if (!\App::config()->product['reviewEnabled'] || !$score) {
            return [];
        }

        $stars = [];

        if (empty($score)) {
            for ($i = 5; $i > ceil($score); $i--) {
                $stars[] = [];
            }
        } else {
            for ($i = 0; $i < (int)$score; $i++) {
                $stars[] = ['star' => true];
            }
            if (ceil($score) > $score) {
                $stars[] = [];
            }
            for ($i = 5; $i > ceil($score); $i--) {
                $stars[] = [];
            }
        }

        return [
            'stars' => $stars,
            'count' => $product->getNumReviews(),
        ];
    }
}
