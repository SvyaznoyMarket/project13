<?php
/**
 * @param \Model\Product\Filter\Entity[] $filters
 */
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
?>

    <div class="fltrBtnBox fl-l js-category-filter-dropBox">
        <div class="fltrBtnBox_tggl icon-corder js-category-filter-dropBox-opener">
            <span class="dotted" style="font-size: 16px; font-family: 'GloberSemiBold'"><?= $filter->getName() ?></span>
        </div>

        <div class="fltrBtnBox_dd fltrBtnBox_dd-l">
            <ul class="fltrBtnBox_dd_inn lstdotted js-category-filter-dropBox-content">
                <? foreach ($filter->getPriceRanges() as $range): ?>
                    <li class="lstdotted_i">
                        <a class="dotted" href="<?= $helper->escape($range['url']) ?>">
                            <? if (isset($range['from'])): ?>
                                от <?= $helper->formatPrice($range['from']) ?>
                            <? endif ?>

                            <? if (isset($range['to'])): ?>
                                до <?= $helper->formatPrice($range['to']) ?>
                            <? endif ?>
                        </a>
                    </li>
                <? endforeach ?>
            </ul>
        </div>
    </div>

<?php };