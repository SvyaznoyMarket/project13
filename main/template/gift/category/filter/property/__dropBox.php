<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $property
) {
    $values = $productFilter->getValue($property);
    $selectedOption = null;
    foreach ($property->getOption() as $option) {
        if (in_array($option->getId(), $values)) {
            $selectedOption = $option;
            break;
        }
    }

    if (!$selectedOption && isset($property->getOption()[0])) {
        $selectedOption = $property->getOption()[0];
    }
?>

    <div class="fltrBtnBox fltrBtnBox-gift js-gift-category-filter-property-dropBox <? if ('sex' === $property->getName()): ?>js-gift-category-filter-property-dropBox-sex<? endif ?> <? if ('status-woman' === $property->getName()): ?>js-gift-category-filter-property-dropBox-status-woman<? endif ?> <? if ('status-man' === $property->getName()): ?>js-gift-category-filter-property-dropBox-status-man<? endif ?>">
        <div class="fltrBtnBox_tggl">
            <span class="js-gift-category-filter-property-dropBox-opener">
                <?= $helper->escape($selectedOption->getName()) ?>
            </span>
            <i class="fltrBtnBox_tggl_corner"></i>
        </div>

        <div class="fltrBtnBox_dd">
            <ul class="fltrBtnBox_dd_inn lstdotted js-gift-category-filter-property-dropBox-content">
                <? foreach ($property->getOption() as $option): ?>
                    <li class="lstdotted_i">
                        <input
                            class="customInput customInput-gift js-gift-category-filter-property-list-radio"
                            type="radio"
                            id="<?= $option->getId() ?>"
                            name="<?= \View\Name::productCategoryFilter($property, $option) ?>"
                            value="<?= $option->getId() ?>"
                            <? if ($option === $selectedOption): ?>
                                checked="checked"
                            <? endif ?>
                        />
                        <label for="<?= $option->getId() ?>" class="customLabel js-gift-category-filter-property-dropBox-content-item">
                            <span class="js-gift-category-filter-property-dropBox-content-item-title">
                                <?= $helper->escape($option->getName()) ?>
                            </span>
                        </label>
                    </li>
                <? endforeach ?>
            </ul>
        </div>
    </div>
<? };