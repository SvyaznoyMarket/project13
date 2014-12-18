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
        $active['url'] = $helper->replacedUrl(['sort' => implode('-', [$active['name'], $active['direction']]), 'ajax' => null]);

        if ($active['name'] == 'default') {
            $active['url'] = $helper->replacedUrl(['sort' => null, 'ajax' => null]);
        }

        foreach ($productSorting->getAll() as $item) {
            // SITE-2244
            // Убрал сортировку по брендам
            if ($item['name'] == 'creator') {
                continue;
            }

            $item['url'] = $helper->replacedUrl(['page' => '1', 'sort' => implode('-', [$item['name'], $item['direction']]), 'ajax' => null]);
            $item['datasort'] = implode('-', [$item['name'], $item['direction']]);

            if ($item['name'] == 'default') {
                $item['url'] = $helper->replacedUrl(['sort' => null, 'ajax' => null]);
                $item['default'] = true;
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