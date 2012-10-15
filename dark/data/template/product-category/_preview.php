<?php
/**
 * @var $category     \Model\Product\Category\Entity
 * @var $rootCategory \Model\Product\Category\Entity
 */
?>
<div class="goodsbox height250">

    <div class="photo">
        <a href="<?= $category->getLink() ?>">
            <img src="<?= $category->getImageUrl() ?>" alt="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" title="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" width="160" height="160"/>
        </a>
    </div>

    <h2><a href="<?= $category->getLink() ?>" class="underline"><?= $category->getName() ?></a></h2>

    <div class="font11">
        <a href="<?= $category->getLink() ?>" class="underline gray"><?= $category->getProductCount() ?> товаров</a>
    </div>

    <!-- Hover -->
    <div class="boxhover">
        <b class="rt"></b><b class="lb"></b>

        <div class="rb">
            <div class="lt" data-url="<?= $category->getLink() ?>">

                <div class="photo">
                    <a href="<?= $category->getLink() ?>">
                        <img src="<?= $category->getImageUrl() ?>" alt="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" title="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" width="160" height="160"/>
                    </a>
                </div>

                <h2><a href="<?= $category->getLink() ?>" class="underline"><?= $category->getName() ?></a></h2>

                <div class="font11">
                    <a href="<?= $category->getLink() ?>" class="underline gray"><?= $category->getProductCount() ?> товаров</a>
                </div>

            </div>
        </div>
    </div>
    <!-- /Hover -->
</div>
