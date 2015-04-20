<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $property
) {
    $title = '';
    $values = $productFilter->getValue($property);
    foreach ($property->getOption() as $option) {
        if (in_array($option->getId(), $values)) {
            if ($title != '') {
                $title .= '...';
                break;
            }

            $title .= $option->getName();
        }
    }

    if ($title == '') {
        $title = $property->defaultTitle;
    }
?>

    <div class="fltrBtnBox <? if ($productFilter->getValue($property)): ?>actv<? endif ?> js-category-v2-filter-dropBox2" data-default-title="<?= $helper->escape(trim($property->defaultTitle)) ?>">
        <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox2-opener">
            <span class="fltrBtnBox_tggl_tx js-category-v2-filter-dropBox2-title"><?= $title ?></span>
            <i class="fltrBtnBox_tggl_corner"></i>
        </div>

        <div class="fltrBtnBox_dd js-category-v2-filter-dropBox2-content">
            <div class="fltrBtnBox_dd_inn">
                <div class="fltrBtn_param">
                    <?= $helper->render('product-category/v2/filter/__element', ['productFilter' => $productFilter, 'filter' => $property]) ?>
                </div>
            </div>
        </div>
    </div>

<? };