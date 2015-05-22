<?php

namespace View\Supplier;


/** Страница регистрации поставщика
 * Class NewPage
 * @package View\Supplier
 */
class NewPage extends SupplierLayout {

    public function slotContent() {
        return $this->render('supplier/page-new', $this->params);
    }

}