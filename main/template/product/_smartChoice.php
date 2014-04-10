<?php

return function(
    \Helper\TemplateHelper $helper,
    array $smartChoiceProducts
) {

    if ($smartChoiceProducts && count($smartChoiceProducts) == 3) :
        $smartChoiceIds = array_map(function($item){
            return $item['product']->getId();
        }, $smartChoiceProducts);

        ?>
        <div class="specialPrice clearfix">
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
        </div>
        <?

    endif;

}; ?>

