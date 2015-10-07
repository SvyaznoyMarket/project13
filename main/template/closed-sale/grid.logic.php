<?php
/**
 * @var [] $currentSales
 */
$twoMidlleUsed = $leftUsed = false;
$canUseThreeSmall = true;

// А теперь логика грида!
while ($currentSales) {
    // если оставшееся количество больше трех
    if (count($currentSales) > 3) {
        // и не использовали левую раскладку
        if (!$leftUsed) {
            echo $page->render('closed-sale/partials/grid.middle-left',
                ['sales' => array_splice($currentSales, 0, 3)]);
        } else if ($canUseThreeSmall) {
            // если можем использовать три маленьких изображения, но не использовали два больших
            if (!$twoMidlleUsed) {
                echo $page->render('closed-sale/partials/grid.two-middle',
                    ['sales' => array_splice($currentSales, 0, 2)]);
                $twoMidlleUsed = true;
            } else {
                // используем три маленьких
                echo $page->render('closed-sale/partials/grid.three-small',
                    ['sales' => array_splice($currentSales, 0, 3)]);
                $canUseThreeSmall = false; // и ставим флаг, что использовать три маленьких нельзя
            }
            $leftUsed = !$leftUsed;
        } else {
            // и использовали левую раскладку
            echo $page->render('closed-sale/partials/grid.middle-right',
                ['sales' => array_splice($currentSales, 0, 3)]);
            $canUseThreeSmall = true; // и ставим флаг, что использовать три маленьких можно, т.к. это конец "цикла"
        }
        // в любом случае поменяем раскладку лево/право
        $leftUsed = !$leftUsed;
    } else if (count($currentSales) === 2) {
        echo $page->render('closed-sale/partials/grid.two-middle',
            ['sales' => array_splice($currentSales, 0, 2)]);
    } else if (count($currentSales) === 1) {
        echo $page->render('closed-sale/partials/grid.one-big', ['sales' => array_splice($currentSales, 0 , 1)]);
    }
}