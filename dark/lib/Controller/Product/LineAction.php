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
        //TODO

        if (!$id) {
            throw new \Exception\NotFoundException(sprintf('Серия с токеном "%s" не найдена.', $lineToken));
        }

        $line = \RepositoryManager::getLine()->getEntityById($id);
        if (!$line) {
            throw new \Exception\NotFoundException(sprintf('Серия с токеном "%s" не найдена.', $lineToken));
        }

        // вид списка других товаров в серии
        $productView = $request->get('view', 'compact');

        $products = array();
        if ((bool)$line->getProductId()) {
            $products = \RepositoryManager::getProduct()->getCollectionById($line->getProductId());
        }

        $kits = array();
        if ((bool)$line->getKitId()) {
            $kits = \RepositoryManager::getProduct()->getCollectionById($line->getKitId());
        }

        $mainProduct = \RepositoryManager::getProduct()->getEntityById($line->getMainProductId());

        $parts = array();
        if ((bool)$mainProduct->getKit()) {
            $partId = array();
            foreach ($mainProduct->getKit() as $part) {
                $partId[] = $part->getId();
            }
            $parts = \RepositoryManager::getProduct()->getCollectionById($partId);
        }

        $productPager = new \Iterator\EntityPager($kits, count($kits));
        $productPager->setMaxPerPage(count($kits));

        $page = new \View\Product\LinePage();
        $page->setParam('line', $line);
        $page->setParam('products', $products);
        $page->setParam('kits', $kits);
        $page->setParam('mainProduct', $mainProduct);
        $page->setParam('parts', $parts);
        $page->setParam('productView', $productView);
        $page->setParam('productPager', $productPager);

        return new \Http\Response($page->show());
    }
}