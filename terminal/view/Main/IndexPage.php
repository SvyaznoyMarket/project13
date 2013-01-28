<?php

namespace Terminal\View\Main;

class IndexPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('main/page-index', $this->params);
    }
}
