<?php

namespace Controller\ShopScript;

class Action {
    public function execute(\Http\Request $request) {
        return new \Http\JsonResponse([
            "seo_title" => "ZZZZZZZZzzzzzzzzzzzzzz",
            "seo_description" => "ZZZZZZZZZZZZzzzzzzzzZZZZZZZZZZZzzzzzzzZZZZZZZZZZzzzzzzzz",
            "seo_keywords" => "zzzZZZ zZZZZZZ ZZZZZZZ Z",
        ]);
    }
}