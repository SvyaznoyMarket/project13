<?php

namespace EnterSite;

use StdClass;

trait DebugContainerTrait {
    /**
     * @return StdClass
     */
    protected function getDebugContainer() {
        if (!isset($GLOBALS[__METHOD__])) {
            $GLOBALS[__METHOD__] = new StdClass();
        }

        return $GLOBALS[__METHOD__];
    }
}