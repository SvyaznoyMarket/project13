<?php

namespace EnterSite;

use EnterSite\Helper;

trait TranslateHelperTrait {
    /**
     * @return Helper\Translate
     */
    protected function getTranslateHelper() {
        if (!isset($GLOBALS[__METHOD__])) {
            $GLOBALS[__METHOD__] = new Helper\Translate();
        }

        return $GLOBALS[__METHOD__];
    }
}