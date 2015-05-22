<?php

namespace View\Supplier;


class NewPage extends \View\DefaultLayout {

    protected $layout  = 'layout-supplier';

    public function slotContent() {
        return $this->render('supplier/page-new', $this->params);
    }

}