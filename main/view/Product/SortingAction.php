<?php

namespace View\Product;

class SortingAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Sorting $productSorting
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Sorting $productSorting
    ) {

        $links = [];

        $active = $productSorting->getActive();
        $active['url'] = $helper->replacedUrl(['sort' => implode('-', [$active['name'], $active['direction']])]);

        if ($active['name'] == 'default' && !empty($inSearch)) {
            $active['url'] = $helper->replacedUrl(['sort' => null]);
        }

        foreach ($productSorting->getAll() as $item) {
            $item['url'] = $helper->replacedUrl(['page' => '1', 'sort' => implode('-', [$item['name'], $item['direction']])]);

            if ($item['name'] == 'default' && !empty($inSearch)) {
                $item['url'] = $helper->replacedUrl(['sort' => null]);
            }

            if ($active['name'] == $item['name'] && $active['direction'] == $item['direction']) {
                $item['active'] = true;
            }

            $links[] = $item;
        }

        return [
            'links' => $links,
        ];
    }
}