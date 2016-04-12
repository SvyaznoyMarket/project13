<?php

namespace View\Subscribe;

class DeletePage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('subscribe/page-delete', $this->params);
    }
}
