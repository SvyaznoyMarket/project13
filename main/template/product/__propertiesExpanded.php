<?php

//print_r($productExpanded);

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\ExpandedEntity $productExpanded,
    $showLinkToProperties = true
) {

    $HtmlProperties = '';
    foreach ($productExpanded->getProperty() as $property):
        $HtmlOut = $property->getName() . ': ' . $property->getStringValue();
        $HtmlOut = $helper->wrap($HtmlOut, '', 'li');
        $HtmlProperties .= $HtmlOut;
    endforeach;

    if ( $HtmlProperties ) {
        $HtmlProperties = $helper->wrap($HtmlProperties, '', 'ul');
    }

    if ($showLinkToProperties) $HtmlProperties .= '<div class="bTextMore"><a class="jsGoToId" data-goto="productspecification" href="">Все характеристики</a></div>';
?>
<div class="bSpecifications">
    <?= $HtmlProperties ?>
</div>
<?
}; //end function