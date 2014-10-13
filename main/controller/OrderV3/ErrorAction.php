<?php


namespace Controller\OrderV3;


class ErrorAction extends OrderV3 {

    public function execute(\Http\Request $request, $step = null, $error = null) {
        $controller = parent::execute($request);
        if ($controller) {
            return $controller;
        }
    }
} 