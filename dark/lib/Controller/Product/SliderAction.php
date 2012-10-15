<?php

namespace Controller\Product;

class SliderAction {
    public function category($categoryPath, \Http\Request $request) {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        return new \Http\JsonResponse(array());
    }

    public function related($productToken, \Http\Request $request) {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        return new \Http\JsonResponse(array());
    }

    public function accessory($productToken, \Http\Request $request) {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        return new \Http\JsonResponse(array());
    }
}