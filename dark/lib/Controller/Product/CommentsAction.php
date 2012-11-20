<?php

namespace Controller\Product;

class CommentsAction {

    public function execute($productPath, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $productToken = explode('/', $productPath);
        $productToken = end($productToken);

        throw new \Exception\NotFoundException(sprintf('Товар с токеном "%s" не найден.', $productToken));
    }
}