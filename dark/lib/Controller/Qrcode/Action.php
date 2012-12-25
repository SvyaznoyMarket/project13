<?php

namespace Controller\Qrcode;

class Action {
    /**
     * @param string        $qrcode Например, '6LAT4'
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($qrcode, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        $qrcode = trim((string)$qrcode);
        if (!$qrcode) {
            throw new \Exception\NotFoundException('Не передан qrcode');
        }

        $data = $client->query('qrcode/get', array('qrcode' => $qrcode));

        $list = isset($data['item_list']) ? (array)$data['item_list'] : array();
        if (!(bool)$list) {
            throw new \Exception\NotFoundException(sprintf('Не получен список элеметов для qrcode %s', $qrcode));
        }

        /** @var $productsById \Model\Product\Entity[] */
        $productsById = array();
        // получаем ид товаров
        foreach ($list as $item) {
            $item = array(
                'id'      => isset($item['id']) ? (int)$item['id'] : null,
                'type_id' => isset($item['type_id']) ? (int)$item['type_id'] : null,
            );

            if (!$item['id']) {
                \App::logger()->warn(sprintf('Пустой элемент типа %s из qrcode %s', $item['type_id'], $qrcode));
                continue;
            }
            if (1 != $item['type_id']) {
                \App::logger()->warn(sprintf('Обработка элемента типа %s из qrcode %s не реализована', $item['type_id'], $qrcode));
                continue;
            }

            $productsById[$item['id']] = null;
        }

        // товары
        foreach (\RepositoryManager::getProduct()->getCollectionById(array_keys($productsById)) as $product) {
            $productsById[$product->getId()] = $product;
        }

        if (1 == count($productsById)) {
            /** @var $product \Model\Product\Entity */
            $product = reset($productsById);
            $action = new \Controller\Product\IndexAction();

            return $action->execute($product->getPath(), $request);
        } else if (count($productsById) > 1) {
            $barcodes = array_map(function($product) { /** @var $product \Model\Product\Entity */ return $product->getBarcode(); }, $productsById);

            $action = new \Controller\Product\SetAction();

            return $action->execute(implode(',', $barcodes), $request);
        }

        throw new \Exception\NotFoundException(sprintf('Не сформирован список товаров для qrcode %s', $qrcode));
    }
}