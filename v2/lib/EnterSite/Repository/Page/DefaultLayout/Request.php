<?php

namespace EnterSite\Repository\Page\DefaultLayout;

use EnterSite\Model;

class Request {
    /** @var Model\Region */
    public $region;
    /** @var Model\MainMenu\Element[] */
    public $mainMenu = [];
}