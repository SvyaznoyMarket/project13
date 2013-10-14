<?php

namespace Terminal\Controller\ProductLine;

class PartAction {
    /**
     * @param $lineId
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute($lineId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        $line = \RepositoryManager::line()->getEntityById($lineId);
        if (!$line) {
            throw new \Exception\NotFoundException(sprintf('Категория #% не найдена', $line->getId()));
        }

        $productIds = $line->getProductId();


        $entityClass = '\Model\Product\TerminalEntity';
        /** @var $partsById \Model\Product\TerminalEntity[] */
        $partsById = [];
        if ((bool)$productIds) {
            \RepositoryManager::product()->prepareCollectionById($productIds, $user->getRegion(), function($data) use(&$partsById, $entityClass) {
                foreach ($data as $item) {
                    $partsById[$item['id']] = new $entityClass($item);
                }
            });
        }

        $mainProduct = null;
        if ($line->getMainProductId()) {
            \RepositoryManager::product()->prepareCollectionById([$line->getMainProductId()], $user->getRegion(), function($data) use(&$mainProduct, $entityClass) {
                if ((bool)$data) {
                    $mainProduct = new $entityClass(reset($data));
                }
            });
        }
        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $page = new \Terminal\View\ProductLine\PartPage();
        $page->setParam('line', $line);
        $page->setParam('partsById', $partsById);
        $page->setParam('mainProduct', $mainProduct);

        return new \Http\Response($page->show());
    }
}
