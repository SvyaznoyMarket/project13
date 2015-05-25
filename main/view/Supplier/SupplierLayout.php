<?php

namespace View\Supplier;


use \View\DefaultLayout;

/** Layout для всех страниц поставщика
 * Class SupplierLayout
 * @package View\Supplier
 */
class SupplierLayout extends DefaultLayout {

    protected $layout  = 'layout-supplier';

    public function __construct()
    {
        parent::__construct();
        $this->addJavascript( \App::config()->debug ? '/js/prod/supplier.js' : '/js/prod/supplier.min.js');
    }


    public function slotBodyClassAttribute()
    {
        return parent::slotBodyClassAttribute() . ' body-supplier';
    }


}