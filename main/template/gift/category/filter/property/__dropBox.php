<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $property,
    $initialValue = null,
    \Model\Product\Filter\Entity $sexProperty = null
) {
    foreach ($property->getOption() as $option) {
        if ($initialValue && \App::request()->query->get(\View\Name::productCategoryFilter($property, $option)) != null) {
            $initialValue = null;
            break;
        }
    }
    
    $selectedOption = null;
    if (!$initialValue) {
        $selectedOption = $property->getSelectedOption($productFilter);
    }

    $manSexStatusOptions = [];
    $womanSexStatusOptions = [];
    $selectedSexOptionToken = null;
    if ($sexProperty && 'status' === $property->getId()) {
        $selectedSexOptionToken = $sexProperty->getSelectedOption($productFilter)->getId() == 687 ? 'woman' : 'man';
        
        $startManSexOptions = false;
        foreach ($property->getOption() as $option) {
            if (698 == $option->getId()) {
                $startManSexOptions = true;
            }
            
            if ($startManSexOptions) {
                $manSexStatusOptions[] = $option;
            } else {
                $womanSexStatusOptions[] = $option;
            }
        }
        
        if ($selectedOption) {
            if ('man' === $selectedSexOptionToken && !in_array($selectedOption, $manSexStatusOptions, true)) {
                $selectedOption = reset($manSexStatusOptions);
            } else if ('woman' === $selectedSexOptionToken && !in_array($selectedOption, $womanSexStatusOptions, true)) {
                $selectedOption = reset($womanSexStatusOptions);
            }
        }
    }
?>

    <div class="fltrBtnBox fltrBtnBox-gift js-gift-category-filter-property-dropBox <? if ('sex' === $property->getId()): ?>js-gift-category-filter-property-dropBox-sex<? endif ?> <? if ('status' === $property->getId()): ?>js-gift-category-filter-property-dropBox-status<? endif ?>">
        <div class="fltrBtnBox_tggl <?= $initialValue ? 'initial' : ''?>">
            <span class="js-gift-category-filter-property-dropBox-opener">
                <?= $helper->escape($initialValue ? $initialValue : $selectedOption->getName()) ?>
            </span>
            <i class="fltrBtnBox_tggl_corner"></i>
        </div>

        <div class="fltrBtnBox_dd">
            <ul class="fltrBtnBox_dd_inn lstdotted js-gift-category-filter-property-dropBox-content">
                <? foreach ($property->getOption() as $option): ?>
                    <? $id = \View\Id::productCategoryFilter($property->getId()) . '-option-' . $option->getId(); ?>
                    <li class="lstdotted_i js-gift-category-filter-property-dropBox-content-item" <? if ('man' === $selectedSexOptionToken && !in_array($option, $manSexStatusOptions, true) || 'woman' === $selectedSexOptionToken && !in_array($option, $womanSexStatusOptions, true)): ?>style="display: none;"<? endif ?>>
                        <input
                            class="customInput customInput-gift js-gift-category-filter-property-list-radio"
                            type="radio"
                            id="<?= $helper->escape($id) ?>"
                            name="<?= \View\Name::productCategoryFilter($property, $option) ?>"
                            value="<?= $option->getId() ?>"
                            <? if ($option === $selectedOption): ?>
                                checked="checked"
                            <? endif ?>
                        />
                        <label for="<?= $helper->escape($id) ?>" class="customLabel js-gift-category-filter-property-dropBox-content-item-clicker">
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