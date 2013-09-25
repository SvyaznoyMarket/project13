<?php

namespace Controller\Jewel\Product;

class SliderAction {
    /**
     * @param \Iterator\EntityPager $pager
     * @param string                $productView
     * @param \Http\Request         $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute(\Iterator\EntityPager $pager, $productView, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        // проверка на максимально допустимый номер страницы
        if (($pager->getPage() - $pager->getLastPage()) > 0) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $pager->getPage()));
        }

        return new \Http\Response(\App::templating()->render('product/_list', array(
            'page'   => new \View\Layout(),
            'pager'  => $pager,
            'view'   => $productView,
            'isAjax' => true,
        )));
    }
}