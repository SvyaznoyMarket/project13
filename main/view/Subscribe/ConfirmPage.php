<?php

namespace View\Subscribe;

class ConfirmPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('subscribe/page-confirm', $this->params);
    }
}
