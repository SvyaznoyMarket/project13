<? if ($category && count($category->getChild()) > 1) : ?>
    <ul class="categories-grid grid-3col">
        <? foreach ($category->getChild() as $childCategory) : ?>
            <li class="categories-grid__item grid-3col__item">
                <a href="<?= $childCategory->getLink() ?>" class="categories-grid__link">
                                <span class="categories-grid__img">
                                    <img src="<?= $childCategory->getImageUrl() ?>" alt="" class="image">
                                </span>

                    <span class="categories-grid__text"><?= $childCategory->getName() ?></span>
                </a>
            </li>
        <? endforeach ?>
    </ul>
<? endif ?>