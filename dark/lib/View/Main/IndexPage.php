<?php

namespace View\Main;

class IndexPage extends \View\DefaultLayout {
    public function slotContent() {
        return $this->render('main/page-index', $this->params);
    }
}
