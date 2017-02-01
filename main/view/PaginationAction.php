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
        $currentPage = $pager->getPage();
        $lastPage = $pager->getLastPage();

        if ($lastPage <= 12) {
            $pageBarConfigs = [
                ['pageBarFirstPage' => 1, 'pageBarSize' => 13],
            ];
        } else if ($lastPage <= 14) {
            $pageBarConfigs = [
                ['pageBarFirstPage' => 1, 'pageBarSize' => 6],
                ['pageBarFirstPage' => $lastPage - 5, 'pageBarSize' => 6],
            ];
        } else {
            $pageBarConfigs = [
                ['pageBarFirstPage' => 1, 'pageBarSize' => 6],
                ['pageBarFirstPage' => floor($lastPage / 2), 'pageBarSize' => 3],
                ['pageBarFirstPage' => $lastPage - 2, 'pageBarSize' => 3],
            ];
        }

        $links = [];
        foreach ($pageBarConfigs as $key => $pageBarConfig) {
            $pageBarFirstPage = $pageBarConfig['pageBarFirstPage'];
            $pageBarLastPage = isset($pageBarConfigs[$key + 1]) ? $pageBarConfigs[$key + 1]['pageBarFirstPage'] - 1 : $lastPage;
            $pageBarSize = $pageBarConfig['pageBarSize'] < $lastPage ? $pageBarConfig['pageBarSize'] : $lastPage;

            $pageBarRealFirstPage = $currentPage >= $pageBarFirstPage && $currentPage <= $pageBarLastPage + 1 ? max($pageBarFirstPage, $currentPage) : $pageBarFirstPage;
            $pageBarVisibleFirstPage = $pageBarRealFirstPage - floor($pageBarSize / 2);
            $pageBarVisibleLastPage = $pageBarRealFirstPage + ceil($pageBarSize / 2) - 1;

            if ($pageBarVisibleFirstPage < $pageBarFirstPage) {
                $pageBarVisibleLastPage += -$pageBarVisibleFirstPage + $pageBarFirstPage;
                $pageBarVisibleFirstPage = $pageBarFirstPage;
            }

            if ($pageBarVisibleLastPage > $pageBarLastPage) {
                $pageBarVisibleFirstPage -= $pageBarVisibleLastPage - $pageBarLastPage;
                $pageBarVisibleLastPage = $pageBarLastPage;
            }

            for ($page = $pageBarVisibleFirstPage; $page <= $pageBarVisibleLastPage; $page++) {
                if ($page == $currentPage) {
                    $links[] = ['name' => $page, 'url' => '#', 'active' => true, 'page' => $page];
                } else {
                    $links[] = ['name' => $page, 'url'  => $helper->replacedUrl(['page' => $page, 'ajax' => null]), 'page' => $page];
                }
            }

            $links[] = ['name' => null];
        }

        if (isset($links[0]['name']) && $links[0]['name'] != 1) {
            $links = array_merge([['name' => null]], $links);
        }

        array_pop($links);

        foreach ($links as $key => $value) {
            if ($links['name'] === null && isset($links[$key - 1]) && isset($links[$key + 1]) && $links[$key - 1]['page'] + 1 == $links[$key + 1]['page']) {
                unset($links[$key]);
            }
        }

        $links = array_values($links);

        return [
            'links' => $links,
            'lastPage' => $lastPage,
            'currentPage' => $currentPage,
            'currentPosition' => $pager->key()
        ];
    }
}