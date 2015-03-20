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

    /** @var \Model\Product\Filter\Option\Entity[] $manSexStatusOptions */
    $manSexStatusOptions = [];
    /** @var \Model\Product\Filter\Option\Entity[] $womanSexStatusOptions */
    $womanSexStatusOptions = [];
    $selectedSexOptionToken = null;
    $statusOptionGroups = [];
    $options = null;
    if ($sexProperty && 'status' === $property->getId()) {
        $sexSelectedOption = $sexProperty->getSelectedOption($productFilter);
        $selectedSexOptionToken = $sexSelectedOption && $sexSelectedOption->getId() == 687 ? 'woman' : 'man';

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

        if ('man' === $selectedSexOptionToken) {
            $options = $manSexStatusOptions;
        } else if ('woman' === $selectedSexOptionToken) {
            $options = $womanSexStatusOptions;
        }

        // Из-за присутствия одинаковых статусов как у мужчины так и у женщине, необходимо  искать $selectedOption в соответствующих выбранному полу статусах
        if (!$initialValue) {
            $selectedOption = $property->getSelectedOption($productFilter, $options);
        }

        foreach ($manSexStatusOptions as $option) {
            $statusOptionGroups['man'][] = [
                'id' => \View\Id::productCategoryFilter($property->getId()) . '-option-' . $option->getId(),
                'name' => \View\Name::productCategoryFilter($property, $option),
                'value' => $option->getId(),
                'title' => $option->getName(),
            ];
        }

        foreach ($womanSexStatusOptions as $option) {
            $statusOptionGroups['woman'][] = [
                'id' => \View\Id::productCategoryFilter($property->getId()) . '-option-' . $option->getId(),
                'name' => \View\Name::productCategoryFilter($property, $option),
                'value' => $option->getId(),
                'title' => $option->getName(),
            ];
        }
    }

    if (!$options) {
        $options = $property->getOption();
    }
?>

    <div class="fltrBtnBox fltrBtnBox-gift js-gift-category-filter-element-dropBox <? if ('holiday' === $property->getId()): ?>js-gift-category-filter-element-dropBox-holiday<? endif ?> <? if ('sex' === $property->getId()): ?>js-gift-category-filter-element-dropBox-sex<? endif ?> <? if ('status' === $property->getId()): ?>js-gift-category-filter-element-dropBox-status<? endif ?>" data-option-groups="<?= $helper->json($statusOptionGroups) ?>">
        <div class="fltrBtnBox_tggl js-gift-category-filter-element-dropBox-opener <?= $initialValue ? 'fltrBtnBox_tggl-dsbld' : ''?>">
            <span class="js-gift-category-filter-element-dropBox-title"><?= $helper->escape($initialValue ? $initialValue : ($selectedOption ? $selectedOption->getName() : '')) ?></span>
            <i class="fltrBtnBox_tggl_corner"></i>
        </div>

        <div class="fltrBtnBox_dd js-gift-category-filter-element-dropBox-content">
            <ul class="fltrBtnBox_dd_inn lstdotted">
                <? foreach ($options as $option): ?>
                    <? $id = \View\Id::productCategoryFilter($property->getId()) . '-option-' . $option->getId(); ?>
                    <li class="lstdotted_i js-gift-category-filter-element-dropBox-content-item">
                        <input
                            class="customInput customInput-gift js-customInput js-gift-category-filter-element-dropBox-content-item-input"
                            type="radio"
                            id="<?= $helper->escape($id) ?>"
                            name="<?= \View\Name::productCategoryFilter($property, $option) ?>"
                            value="<?= $option->getId() ?>"
                            <? if ($option === $selectedOption): ?>
                                checked="checked"
                            <? endif ?>
                            data-id="<?= $helper->escape($property->getId()) ?>"
                        />
                        <label for="<?= $helper->escape($id) ?>" class="customLabel customLabel-gift js-gift-category-filter-element-dropBox-content-item-clicker js-gift-category-filter-element-dropBox-content-item-title">
                            <?= $helper->escape($option->getName()) ?>
                        </label>
                    </li>
                <? endforeach ?>
            </ul>
        </div>
    </div>

<? };