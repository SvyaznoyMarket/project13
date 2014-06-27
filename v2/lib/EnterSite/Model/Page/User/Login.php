<?php

namespace EnterSite\Model\Page\User {
    use EnterSite\Model\Page;

    class Login extends Page\DefaultLayout {
        /** @var Login\Content */
        public $content;

        public function __construct() {
            parent::__construct();

            $this->content = new Login\Content();
        }
    }
}

namespace EnterSite\Model\Page\User\Login {
    use EnterSite\Model\Page;
    use EnterSite\Model\Partial;

    class Content extends Page\DefaultLayout\Content {
        public function __construct() {
            parent::__construct();
        }
    }
}

