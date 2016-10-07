<?php

namespace View;

class PaginationAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Iterator\EntityPager $pager
     * @param \Model\Product\Category\Entity|null $category
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Iterator\EntityPager $pager,
        \Model\Product\Category\Entity $category = null
    ) {
        $first = 1;
        $last = $pager->getLastPage();
        $current = $pager->getPage();
        if ($category && $category->getAvailableForSwitchingViews()) {
            $onSides = 1;
        } else {
            $onSides = 2;
        }

        $pageData = [];

        if ($current > ($first + $onSides)) {
            $pageData[] = ['name' => $first, 'url'  => $helper->replacedUrl(['page' => $first, 'ajax' => null]), 'page' => $first];
            if ($current > ($first + $onSides + 1)) {
                $pageData[] = ['name' => null];
            }
        }

        foreach (range($first, $last) as $num) {
            if ($num == $current) {
                $pageData[] = ['name' => $num, 'url' => '#', 'active' => true, 'page' => $num];
            } else if ($num >= $current - $onSides && $num <= $current + $onSides) {
                $pageData[] = ['name' => $num, 'url'  => $helper->replacedUrl(['page' => $num, 'ajax' => null]), 'page' => $num];
            }
        }

        if ($current < ($last - $onSides)) {
            if ($current < ($last - $onSides - 1)) {
                $pageData[] = ['name' => null];
            }

            $pageData[] = ['name' => $last, 'url'  => $helper->replacedUrl(['page' => $last, 'ajax' => null]), 'page' => $last];
        }

        return [
            'links' => $pageData,
            'lastPage' => $pager->getLastPage(),
            'currentPage' => $current,
            'currentPosition' => $pager->key()
        ];
    }
}