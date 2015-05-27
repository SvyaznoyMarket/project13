<?php

return function(
    \Helper\TemplateHelper $helper,
    array $smartChoiceProducts
) {

    if (!$smartChoiceProducts && !count($smartChoiceProducts) == 3) return '';

    $smartChoiceProducts = array_filter($smartChoiceProducts, function($item) {
        return !empty($item['product']);
    });

    $smartChoiceIds = array_map(function($item){
        return $item['product']->getId();
    }, $smartChoiceProducts);

    $showAction = new \View\Product\SmartChoiceAction();
    $typeMod = [
        'Хит продаж' => '',
        'Выгодное предложение' => 'mProfit',
        'Самым разборчивым' => 'mSpec'
    ];

    $cartButtonAction = new \View\Cart\ProductButtonAction();

?>

    <div class="specialPrice clearfix js-specialPrice">
        <? foreach ($smartChoiceProducts as $key => $product): ?>
            <?= $helper->renderWithMustache('product/_smartChoice', array(
                'product' =>  $showAction->execute($helper, $product['product'], null, $cartButtonAction),
                'name' => $product['name'],
                'typeMod' => $typeMod[$product['name']],
                'first' => $key == 0,
                'last' => $key == 2,
            )); ?>
        <?  endforeach  ?>
    </div>

    <div class="specialBorderBox jsDataSmartChoice" data-smartchoice="<?= $helper->json($smartChoiceIds); ?>"></div>

<? }; ?>

