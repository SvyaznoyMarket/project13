<?php

namespace Controller\ProductCategory;

class SliceAction {
    /**
     * @param \Http\Request $request
     * @param string        $sliceToken
     * @return \Http\Response
     */
    public function execute(\Http\Request $request, $sliceToken) {
        return (new \Controller\Slice\ShowAction())->execute($request, $sliceToken);
    }
}