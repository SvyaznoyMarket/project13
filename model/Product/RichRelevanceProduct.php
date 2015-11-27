<?php

namespace Model\Product;

class RichRelevanceProduct extends Entity
{
    /**
     * @var string
     */
    public $clickUrl;
    /**
     * @var string
     */
    public $clickTrackingUrl;

    public function __construct(array $data)
    {
        parent::__construct($data);
        if (array_key_exists('clickURL', $data)) $this->clickUrl = $data['clickURL'];
        if (array_key_exists('clickTrackingURL', $data)) $this->clickTrackingUrl = $data['clickTrackingURL'];
    }
}
