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
<div class="brandMenuWrap">
    <div class="brandMenu"<?= $css ? "style='$css'": '' ?>>

        <div class="brandLogo"<?= $brandLogo ?>>
            <?= $categoryName ?>
        </div>

        <div class="slideCategory">
            <div class="slideCategory__inner">
                <? if (!empty($categoryList)): ?>
                <ul class="categoryList">
                    <? foreach ($categoryList as $categoryItem) { ?>
                    <li class="categoryList__item">
                        <a class="categoryList__link" href="<?= $categoryItem['link'] ?>"><?= $categoryItem['name'] ?></a>
                    </li>
                    <? } ?>
                    <?/*
                    <li class="categoryList__item"><a class="categoryList__link" href="">Чистота сама по себе</a></li>
                    <li class="categoryList__item"><a class="categoryList__link" href="">Уход за одеждой и шитье</a></li>
                    <li class="categoryList__item"><a class="categoryList__link" href="">Дизайн для ванной и душа</a></li>
                    <li class="categoryList__item"><a class="categoryList__link" href="">Кухни</a></li>
                    <li class="categoryList__item"><a class="categoryList__link" href="">Уход за одеждой и шитье</a></li>
                    <li class="categoryList__item"><a class="categoryList__link" href="">Идеальный завтрак</a></li>
                    <li class="categoryList__item"><a class="categoryList__link" href="">Чистота сама по себе</a></li>
                    <li class="categoryList__item"><a class="categoryList__link" href="">Уход за одеждой и шитье</a></li>
                    */?>
                </ul>
                <? endif; ?>
            </div>
        </div>

    </div>
</div>

<? } ?>
