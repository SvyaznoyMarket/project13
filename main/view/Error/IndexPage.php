<?php

namespace View\Error;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';
    
    public function slotContent() {
        return $this->render('error/page-index', $this->params);
    }
}
