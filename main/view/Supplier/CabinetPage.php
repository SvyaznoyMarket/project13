<?php

namespace View\Supplier;


/** Страница загрузки файлов и редактирования информации
 * Class CabinetPage
 * @package View\Supplier
 */
class CabinetPage extends SupplierLayout {

    public function slotContent() {
        return $this->render('supplier/page-cabinet', $this->params);
    }

}