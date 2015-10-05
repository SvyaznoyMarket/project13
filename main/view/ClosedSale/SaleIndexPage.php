<?php

namespace View\ClosedSale;


use \View\DefaultLayout;

class SaleIndexPage extends DefaultLayout
{

    public function slotContent() {
        return $this->render('closed-sale/sale-index', $this->params);
    }

}