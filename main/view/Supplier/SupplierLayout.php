<?php

namespace View\Supplier;


use \View\DefaultLayout;

/** Layout для всех страниц поставщика
 * Class SupplierLayout
 * @package View\Supplier
 */
class SupplierLayout extends DefaultLayout {

    protected $layout  = 'layout-supplier';

    public function slotBodyClassAttribute()
    {
        return parent::slotBodyClassAttribute() . ' body-supplier';
    }


}