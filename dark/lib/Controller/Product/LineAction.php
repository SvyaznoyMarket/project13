<?php

namespace Controller\Product;

class LineAction {
    public function execute($lineToken, \Http\Request $request) {
        //$line = \RepositoryManager::getLine()->getEntityByToken($lineToken);
        //TODO: выпилить костыль, когда на ядре появится метод получени линии по токену
        $id = null;
        $db = mysql_connect('localhost', 'root', 'qazwsxedc');
        mysql_select_db('enter', $db);
        $result = mysql_query('SELECT `core_id` FROM `product_line` WHERE `token` = \'' . mysql_real_escape_string($lineToken) . '\'');
        if ($row = mysql_fetch_assoc($result)) {
            $id = $row['core_id'];
        } else {
            $matches = array();
            preg_match('/\w+-(?P<id>\d+)/', $lineToken, $matches);
           if (count($matches)) {
               $id = $matches['id'];
           }
        }
        if (!$id) {
            throw new \Exception\NotFoundException(sprintf('Серия с токеном "%s" не найдена.', $lineToken));
        }

        $line = \RepositoryManager::getLine()->getEntityById($id);
        if (!$line) {
            throw new \Exception\NotFoundException(sprintf('Серия с токеном "%s" не найдена.', $lineToken));
        }

        $products = array();
        if ((bool)$line->getProductId()) {
            $products = \RepositoryManager::getProduct()->getCollectionById($line->getProductId());
        }

        $kits = array();
        if ((bool)$line->getKitId()) {
            $kits = \RepositoryManager::getProduct()->getCollectionById($line->getKitId());
        }

        $mainProduct = \RepositoryManager::getProduct()->getEntityById($line->getMainProductId());

        $page = new \View\Product\LinePage();
        $page->setParam('line', $line);
        $page->setParam('products', $products);
        $page->setParam('kit', $kits);

        return new \Http\Response($page->show());
    }
}