<?php

namespace View\Product;

class ReviewCompactAction {
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product
    ) {
        $score = $avgStarScore = $product->getAvgStarScore();

        if (!\App::config()->product['reviewEnabled'] || !$score) {
            return [];
        }

        $defaultImage = "/images/reviews_star.png";
        $halfImage = "/images/reviews_star_half.png";
        $emptyImage = "/images/reviews_star_empty.png";

        $stars = [];
        if (empty($score)) {
            for ($i = 5; $i > ceil($score); $i--) {
                $stars[] = ['image' => $emptyImage];
            }
        } else {
            for ($i = 0; $i < (int)$score; $i++) {
                $stars[] = ['image' => $defaultImage];
            }
            if (ceil($score) > $score) {
                $stars[] = ['image' => $halfImage];
            }
            for ($i = 5; $i > ceil($score); $i--) {
                $stars[] = ['image' => $emptyImage];
            }
        }

        return [
            'stars' => $stars,
            'count' => $product->getNumReviews(),
        ];
    }
}