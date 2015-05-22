<?php

namespace View\Supplier;


class CabinetPage extends \View\DefaultLayout {

    protected $layout  = 'layout-supplier';

    public function slotContent() {
        return $this->render('supplier/page-cabinet', $this->params);
    }

}