<?php

namespace EnterSite;

use EnterSite\Helper;

trait UrlHelperTrait {
    /**
     * @return Helper\Url
     */
    protected function getUrlHelper() {
        if (!isset($GLOBALS[__METHOD__])) {
            $GLOBALS[__METHOD__] = new Helper\Url();
        }

        return $GLOBALS[__METHOD__];
    }
}