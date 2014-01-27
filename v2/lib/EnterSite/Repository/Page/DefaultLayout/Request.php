<?php

namespace EnterSite\Repository\Page\DefaultLayout;

use EnterSite\Model;

class Request {
    /** @var Model\MainMenu[] */
    public $mainMenuList = [];
    /** @var Model\Region */
    public $region;
}