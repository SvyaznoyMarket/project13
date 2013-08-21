<?php

/* Файл не используется пока нигде, но может понадобиться в будущем. Правда, возможно стоит прорефакторить либо удалить */
/* Ecли будет использоваться, нужно переделать генерацию хтмл */

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    \Model\Product\ExpandedEntity $productExpanded = null
) {

    $groupedProperties = $product->getGroupedProperties();


    $mainProperties = [];
    foreach ($productExpanded->getProperty() as $property) $mainProperties[] = $property->getId();


    $HtmlGroupedProperties = '';
    foreach ($groupedProperties as $group):
        if (!(bool)$group['properties']) continue;
        $groupName = $group['group']->getName();

        $HtmlOut = '';
        foreach ($group['properties'] as $property):
            /** @var $property \Model\Product\Property\Entity */
            if (in_array($property->getId(), $mainProperties)):

                $dd = $property->getName();
                if ($property->getHint()) {
                    $dd .= $helper->render('__hint', ['name' => $property->getName(), 'value' => $property->getHint()]);
                }
                $dd = '<span>' . $dd. '</span>';
                $dd = '<dd>' . $dd . '</span>';

                $dt = $property->getStringValue();
                if ($property->getValueHint()) {
                    $dt .= $helper->render('__hint', ['name' => $property->getStringValue(), 'value' => $property->getValueHint()]);
                }
                $dt = '<dt>' . $dt . '</dt>';

                $HtmlOut = $dd . $dt;
                $HtmlOut = '<dl class="bSpecifications__eList clearfix">' . $HtmlOut . '</dl>';

            endif; // ins_array()

        endforeach; // $group['properties']

        if (!empty($HtmlOut)) $HtmlGroupedProperties .= '<div class="bSpecifications__eHead">' . $groupName . '</div>' . $HtmlOut;

    endforeach; // $groupedProperties


    ?>
    <h3 id="productspecification" class="bHeadSection">Характеристики</h3>
    <div class="bSpecifications">
        <?= $HtmlGroupedProperties ?>
    </div><!--/product specifications section -->

<? }; //end function