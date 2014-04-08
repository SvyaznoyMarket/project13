<?php

return function(
    \Helper\TemplateHelper $helper,
    array $smartChoiceProducts
) {

    if (count($smartChoiceProducts) == 3) :
        $smartChoiceIds = array_map(function($item){
            return $item['product']->getId();
        }, $smartChoiceProducts);

        ?>
        <div class="specialPrice">
        <?

        $showAction = new \View\Product\SmartChoiceAction();
        $typeMod = [
            'Хит продаж' => '',
            'Выгодное предложение' => 'mProfit',
            'Самым разборчивым' => 'mSpec'
        ];

        $cartButtonAction = new \View\Cart\ProductButtonAction();

        foreach ($smartChoiceProducts as $key => $product) {
            $productShowArr = $showAction->execute($helper, $product['product'], null, $cartButtonAction);
            echo $helper->renderWithMustache(
                'product/_smartChoice', array(
                    'product' => $productShowArr,
                    'name' => $product['name'],
                    'typeMod' => $typeMod[$product['name']],
                    'first'=>$key == 0,
                    'last' => $key == 2,
                ));
        }

        ?>
        </div>

        <div class="specialBorderBox jsDataSmartChoice" data-smartchoice="<?= $helper->json($smartChoiceIds); ?>">
            <!-- Сюда нужно вывести реальный слайдер и передать мне на доработку -->
<!--            <div class="bSlider">
                <div class="bSlider__eInner">
                    <ul class="bSlider__eList clearfix" style="width: 1200px; left: 0px;">

                    </ul>
                </div>

                <div class="bSlider__eBtn mPrev mDisabled"><span></span></div>
                <div class="bSlider__eBtn mNext"><span></span></div>
            </div>-->
        </div>
        <?

    endif;

}; ?>

