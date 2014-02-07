<?php

namespace EnterSite;

use Enter\Helper;

trait ViewHelperTrait {
    /**
     * @return Helper\View
     */
    protected function getHelper() {
        if (!isset($GLOBALS[__METHOD__])) {
            $GLOBALS[__METHOD__] = new Helper\View;
        }

        return $GLOBALS[__METHOD__];
    }
}