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

        /** @var $parts \Model\Product\TerminalEntity[] */
        $parts = [];
        $entityClass = '\Model\Product\TerminalEntity';
        if ((bool)$productIds) {
            \RepositoryManager::product()->prepareCollectionById($productIds, $user->getRegion(), function($data) use(&$parts, $entityClass) {
                foreach ($data as $item) {
                    $parts[] = new $entityClass($item);
                }
            });
        }
        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $page = new \Terminal\View\ProductLine\PartPage();
        $page->setParam('line', $line);
        $page->setParam('parts', $parts);

        return new \Http\Response($page->show());
    }
}
