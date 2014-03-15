<?php

namespace EnterSite;

use Enter\Helper;

trait DateHelperTrait {
    /**
     * @return Helper\Date
     */
    protected function getHelper() {
        if (!isset($GLOBALS[__METHOD__])) {
            $GLOBALS[__METHOD__] = new Helper\Date();
        }

        return $GLOBALS[__METHOD__];
    }
}