<?php

namespace EnterSite;

use Enter\Helper;

trait ViewHelperTrait {
    /**
     * @return Helper\View
     */
    protected function getViewHelper() {
        if (!isset($GLOBALS[__METHOD__])) {
            $GLOBALS[__METHOD__] = new Helper\View();
        }

        return $GLOBALS[__METHOD__];
    }
}