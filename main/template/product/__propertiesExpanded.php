<?php

//print_r($productExpanded);

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\ExpandedEntity $productExpanded,
    $showLinkToProperties = true
) {

    $HtmlProperties = '';
    foreach ($productExpanded->getProperty() as $property):
        $HtmlOut = '';
        $HtmlOut .= '<dd><span>' . $property->getName() . '</span></dd>';
        $HtmlOut .= '<dt><span>' . $property->getStringValue() . '</span></dt>';

        //$HtmlOut = $helper->wrap($HtmlOut, '', 'li');
        $HtmlProperties .= $HtmlOut;
    endforeach;

    if ( $HtmlProperties ) {
        $HtmlProperties = $helper->wrap($HtmlProperties, 'bSpecifications__eList clearfix', 'dl');
    }

    if ($showLinkToProperties) $HtmlProperties .= '<div class="bTextMore"><a class="jsGoToId" data-goto="productspecification" href="">Все характеристики</a></div>';
?>
    <div class="jewel mb15">
        <div class="bSpecifications">
            <?= $HtmlProperties ?>
        </div>
    </div>
<?
}; //end function