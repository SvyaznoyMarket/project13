<?php

namespace View\ClosedSale;


use \View\DefaultLayout;

class SaleShowPage extends DefaultLayout
{

    public function slotContent() {
        return $this->render('closed-sale/sale-one', $this->params);
    }

}