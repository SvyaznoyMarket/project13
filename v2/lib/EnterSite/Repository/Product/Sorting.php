<?php

namespace EnterSite\Repository\Product;

use Enter\Http;
use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Model;

class Sorting {
    use JsonDecoderTrait;
    use ConfigTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @param Http\Request $request
     * @return Model\Product\Sorting|null
     */
    public function getObjectByHttpRequest(Http\Request $request) {
        $sorting = null;

        $data = explode('-', $request->query['sort']);
        if (isset($data[0]) && isset($data[1])) {
            $sorting = new Model\Product\Sorting();
            $sorting->token = $data[0];
            $sorting->direction = $data[1];
        }

        return $sorting;
    }

    /**
     * @return Model\Product\Sorting[]
     */
    public function getObjectList() {
        $sortings = [];

        $data = $this->jsonToArray(file_get_contents($this->getConfig()->dir . '/v2/data/cms/v2/catalog/sorting.json'));
        foreach ($data as $item) {
            $item = array_merge([
                'token'     => null,
                'name'      => null,
                'direction' => null,
            ], $item);

            if (!$item['token'] || !$item['name'] || !$item['direction']) {
                $this->getLogger()->push(['type' => 'error', 'error' => 'Неверный элемент сортировки', 'item' => $item, 'action' => __METHOD__, 'tag' => ['repository']]);
                continue;
            }

            $sorting = new Model\Product\Sorting();
            $sorting->name = $item['name'];
            $sorting->token = $item['token'];
            $sorting->direction = $item['direction'];

            $sortings[] = $sorting;
        }

        return $sortings;
    }
}