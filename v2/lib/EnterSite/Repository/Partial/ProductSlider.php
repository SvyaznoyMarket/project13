<?php

namespace EnterSite\Repository\Partial;

use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductSlider {
    /**
     * @param string $name
     * @param string|null $url
     * @return Model\Partial\ProductSlider
     */
    public function getObject(
        $name,
        $url = null
    ) {
        $slider = new Partial\ProductSlider();

        $slider->widgetId = self::getWidgetId($name);
        $slider->dataUrl = $url;
        $slider->dataName = $name;

        return $slider;
    }

    /**
     * @param $name
     * @return string
     */
    public static function getWidgetId($name) {
        return 'id-productSlider-' . $name;
    }
}