<?php

namespace Model\Product;

class RichRelevanceProduct extends Entity
{
    /**
     * @var string|null
     */
    public $clickUrl;
    /**
     * @var string|null
     */
    public $clickTrackingUrl;

    public function __construct(array $data)
    {
        parent::__construct($data);
        if (array_key_exists('clickURL', $data)) $this->clickUrl = $data['clickURL'];
        if (array_key_exists('clickTrackingURL', $data)) $this->clickTrackingUrl = $data['clickTrackingURL'];
    }

    /**
     * @return string
     */
    public function getOnClickTag()
    {
        return $this->clickTrackingUrl
            ? sprintf(' onclick="(new Image).src=\'%s\';" ', $this->clickTrackingUrl)
            : '';
    }
}
