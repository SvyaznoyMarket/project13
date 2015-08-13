<ul class="categories-grid grid-3col">
    <? foreach ($links as $link) : ?>
        <li class="categories-grid__item grid-3col__item <?= $link['active'] ? 'active' : '' ?>">
            <a href="<?= $link['url'] ?>" class="categories-grid__link">
                                <span class="categories-grid__img">
                                    <img src="<?= $link['image'] ?>" alt="" class="image">
                                </span>

                <span class="categories-grid__text"><?= $link['name'] ?></span>
            </a>
        </li>
    <? endforeach ?>
</ul>