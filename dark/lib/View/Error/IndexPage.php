<?php

namespace View\Error;

class IndexPage extends \View\DefaultLayout {
    public function slotContent() {
        return $this->render('error/page-index', $this->params);
    }
}
