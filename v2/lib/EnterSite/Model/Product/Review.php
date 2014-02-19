<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;

class Review {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $productId;
    /** @var float */
    public $score;
    /** @var float */
    public $starScore;
    /** @var string */
    public $extract;
    /** @var string */
    public $pros;
    /** @var string */
    public $cons;
    /** @var string */
    public $author;
    /** @var string */
    public $source;
    /** @var \DateTime|null */
    public $createdAt;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('product_id', $data)) $this->productId = (string)$data['product_id'];
        if (array_key_exists('score', $data)) $this->score = (float)$data['score'];
        if (array_key_exists('star_score', $data)) $this->starScore = (float)$data['star_score'];
        if (array_key_exists('extract', $data)) $this->extract = (string)$data['extract'];
        if (array_key_exists('pros', $data)) $this->pros = (string)$data['pros'];
        if (array_key_exists('cons', $data)) $this->cons = (string)$data['cons'];
        if (array_key_exists('author', $data)) $this->author = (string)$data['author'];
        if (array_key_exists('origin', $data)) $this->source = (string)$data['origin'];
        if (array_key_exists('date', $data)) $this->createdAt = \DateTime::createFromFormat("Y-m-d", $data['date']) ?: null;
    }
}