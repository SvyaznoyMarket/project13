<?php

namespace EnterSite;

use Enter\Helper;

trait DateHelperTrait {
    /**
     * @return Helper\date
     */
    protected function getHelper() {
        if (!isset($GLOBALS[__METHOD__])) {
            $GLOBALS[__METHOD__] = new Helper\Date();
        }

        return $GLOBALS[__METHOD__];
    }
}