<?php

namespace View;

class PaginationAction {
    public function execute(
        \Helper\TemplateHelper $helper,
        \Iterator\EntityPager $pager
    ) {
        $first = 1;
        $last = $pager->getLastPage();
        $current = $pager->getPage();

        $pageData = [];

        if ($current > ($first + 2)) {
            $pageData[] = ['name' => $first, 'url'  => $helper->replacedUrl(['page' => $first, 'ajax' => null])];
            $pageData[] = ['name' => null];
        }

        foreach (range($first, $last) as $num) {
            if ($num == $current) {
                $pageData[] = ['name' => $num, 'url' => '#', 'active' => true];
            } else if ($num >= $current - 2 && $num <= $current + 2) {
                if (in_array($last, [2, 3]) && $last == $num) {
                    $pageData[] = ['name' => null];
                }

                $pageData[] = ['name' => $num, 'url'  => $helper->replacedUrl(['page' => $num, 'ajax' => null])];
            }
        }

        if ($current < $last - 2) {
            if ($last > 4) {
                $pageData[] = ['name' => null];
            }

            $pageData[] = ['name' => $last, 'url'  => $helper->replacedUrl(['page' => $last, 'ajax' => null])];
        }

        return [
            'links' => $pageData,
        ];
    }
}