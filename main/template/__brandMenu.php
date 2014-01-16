<?php

return function(
    \Helper\TemplateHelper $helper,
    $css = '',
    $brandLogo = '',
    $categoryName,
    array $categoryList = []
) {

    if (!empty($brandLogo)) {
        $brandLogo = sprintf(' style="background-image: url(%s);" ', $brandLogo);
    }
?>
<!-- Меню-слайдер подкатегорий, категории Чибо -->
<div class="tchiboMenuWrap">
    <div class="tchiboMenu"<?= $css ? "style='$css'": '' ?>>

        <div class="tchiboLogo"<?= $brandLogo ?>>
            <?= $categoryName ?>
        </div>

        <div class="slideCategory">
            <div class="tdRelative">
                <? if (!empty($categoryList)): ?>
                <ul class="categoryList">
                    <? foreach ($categoryList as $categoryItem) { ?>
                    <li class="categoryList__item">
                        <a class="categoryList__link" href="<?= $categoryItem['link'] ?>"><?= $categoryItem['name'] ?></a>
                    </li>
                    <? } ?>
                </ul>
                <? endif; ?>
                <div class="sliderBtn mLeftBtn"><a class="sliderBtn__link" href=""></a></div>
            </div>
        </div>
        <div class="sliderBtn mRightBtn"><a class="sliderBtn__link" href=""></a></div>
    </div>
</div>
<!-- /Меню-слайдер подкатегорий, категории Чибо -->

<? } ?>
