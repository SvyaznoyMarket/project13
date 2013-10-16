<?php

namespace Controller\Slice;

class ShowAction {
    public function execute($sliceToken, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $slice = null;
        \RepositoryManager::slice()->prepareEntityByToken($sliceToken, function($data) use (&$slice) {
            if (is_array($data) && (bool)$data) {
                $slice = new \Model\Slice\Entity($data);
            }
        });
        \App::dataStoreClient()->execute();

        if (!$slice) {
            throw new \Exception\NotFoundException(sprintf('Срез @%s не найден', $sliceToken));
        }

        die(var_dump($slice));

        $page = new \View\Slice\ShowPage();
        $page->setParam('slice', $slice);

        return new \Http\Response($page->show());
    }
}