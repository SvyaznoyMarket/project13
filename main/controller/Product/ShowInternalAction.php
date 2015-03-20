<?php

namespace Controller\Product;

class ShowInternalAction {
    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $criteria = [];
        if ($ui = $request->get('ui')) {
            $criteria['ui'] = $ui;
        }

        if (!$criteria) {
            throw new \Exception('Неверный критерий товара', 400);
        }

        /** @var \Model\Product\Entity|null $product */
        $product = null;
        if (isset($criteria['ui'])) {
            \RepositoryManager::product()->prepareEntityByUid($criteria['ui'], function($data) use (&$product) {
                if (isset($data[0]['id'])) {
                    $product = new \Model\Product\Entity($data[0]);
                }
            });
        }

        \App::coreClientV2()->execute();

        if (!$product) {
            throw new \Exception\NotFoundException('Товар не найден');
        }

        return (new \Controller\Product\IndexAction())->execute($product->getLink(), $request);
    }
}