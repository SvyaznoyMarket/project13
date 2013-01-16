<?php

namespace View\Service;

class Helper extends \View\Helper {
    public function categoryIconClass(\Model\Product\Service\Category\Entity $category) {
        if (false !== strpos($category->getToken(), 'mebel')) {
            return 'icon1';
        } else if (false !== strpos($category->getToken(), 'bitovaya-tehnika')) {
            return 'icon2';
        } else if (false !== strpos($category->getToken(), 'elektronika')) {
            return 'icon3';
        } else if (false !== strpos($category->getToken(), 'sport')) {
            return 'icon4';
        }

        return '';
    }
}