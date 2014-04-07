<?php

return function(
    \Helper\TemplateHelper $helper,
    array $smartChoiceProducts
) {

    if (count($smartChoiceProducts) == 3) :
        ?>
        <div class="clearfix">
        <?

        $showAction = new \View\Product\SmartChoiceAction();
        foreach ($smartChoiceProducts as $product) {
            echo $product['name'].'<br />';
            $productShowArr = $showAction->execute($helper, $product['product']);
            echo $helper->renderWithMustache('product/_smartChoice', $productShowArr);
        }

        ?>
        </div>
        <div class="smartChoiceForProduct clearfix">
            
        </div>
        <?

    endif;

}; ?>

