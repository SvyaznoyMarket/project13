<?php
/**
 * @var $categories []
 */
$twoMidlleUsed = $leftUsed = false;
$canUseThreeSmall = true;

// А теперь логика грида!
while ($categories) {
    // если оставшееся количество больше трех
    if (count($categories) >= 3) {
        // и не использовали левую раскладку
        if (!$leftUsed) {
            echo $page->render('product-category/grid/grid-partials/grid.middle-left',
                ['categories' => array_splice($categories, 0, 3)]);
        } else if ($canUseThreeSmall) {
            // если можем использовать три маленьких изображения, но не использовали два больших
            // если число оставшихся акций будет равно 4, то получится грид с двумя строками по два блока, bad
            if (!$twoMidlleUsed && count($categories) !== 4) {
                echo $page->render('product-category/grid/grid-partials/grid.two-middle',
                    ['categories' => array_splice($categories, 0, 2)]);
                $twoMidlleUsed = true;
            } else {
                // используем три маленьких
                echo $page->render('product-category/grid/grid-partials/grid.three-small',
                    ['categories' => array_splice($categories, 0, 3)]);
                $canUseThreeSmall = false; // и ставим флаг, что использовать три маленьких нельзя
            }
            $leftUsed = !$leftUsed;
        } else {
            // и использовали левую раскладку
            echo $page->render('product-category/grid/grid-partials/grid.middle-right',
                ['categories' => array_splice($categories, 0, 3)]);
            $canUseThreeSmall = true; // и ставим флаг, что использовать три маленьких можно, т.к. это конец "цикла"
        }
        // в любом случае поменяем раскладку лево/право
        $leftUsed = !$leftUsed;
    } else if (count($categories) === 2) {
        echo $page->render('product-category/grid/grid-partials/grid.two-middle',
            ['categories' => array_splice($categories, 0, 2)]);
    } else if (count($categories) === 1) {
        echo $page->render('product-category/grid/grid-partials/grid.one-big', ['categories' => array_splice($categories, 0 , 1)]);
    }
}