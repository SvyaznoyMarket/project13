<?php

namespace EnterSite\Repository\Page\DefaultLayout;

use EnterSite\Model;

class Request {
    /** @var Model\Region */
    public $region;
    /** @var \EnterSite\Model\MainMenu\Element[] */
    public $mainMenuList = [];
}