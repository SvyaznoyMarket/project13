<?php

namespace EnterSite\Model\Partial {
    use EnterSite\Model\Partial;

    class UserBlock {
        /** @var string */
        public $widgetId;
        /** @var bool */
        public $isAuthorized;
        /** @var Partial\Link */
        public $userLink;

        public function __construct() {
            $this->widgetId = 'id-userBlock';
            $this->userLink = new Partial\Link();
        }
    }
}
