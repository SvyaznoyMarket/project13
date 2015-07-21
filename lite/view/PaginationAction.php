<?php

namespace view;


class PaginationAction
{

    public function execute(
        \Iterator\EntityPager $pager
    ) {
        $helper = \App::helper();
        $first = 1;
        $last = $pager->getLastPage();
        $current = $pager->getPage();
        $diff = 2;

        // допустим мы на третьей странице из 10
        $range = range($current - $diff, $current + $diff); // формируем массив от текущей страницы => [1,2,3,4,5]
        $range = array_filter($range, function($item) use ($first, $last) { return $item > $first && $item < $last; }); // отбрасываем лишние элементы => [2,3,4,5]
        if (reset($range) > $first + 1) array_unshift($range, 0); // если текущая страница больше 2, то ставим ... в начале => [2,3,4,5]
        if (count($range) && end($range) < $last - 1) $range[] = 0; // если текущая страница меньше 9, то ставим ... в конце => [2,3,4,5,0]
        array_unshift($range, 1);
        if ($first != $last) $range[] = $last; // добиваем первой и последней страницей => [1,2,3,4,5,0,10]

        // формируем массив для Mustache
        $data = ['items' => []];

        foreach ($range as $i) {
            $data['items'][] = [
                'isActive' => $i == $current,
                'link'      => $i != 0 ? $helper->replacedUrl(['page' => $i, 'ajax' => null]) : null,
                'text'          => $i != 0 ? $i : '&#8230;'
            ];
        }

        return $data;
    }
}