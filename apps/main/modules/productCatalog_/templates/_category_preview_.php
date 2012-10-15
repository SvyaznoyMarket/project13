<?php
/**
 * @var ProductCategoryEntity $category
 * @var ProductCategoryEntity $rootCategory
 */
?>
<div class="goodsbox height250">

  <div class="photo">
    <a href="<?php echo $category->getLink() ?>">
      <img src="<?php echo $category->getMediaImageUrl() ?>"
           alt="<?php echo $category->getName() ?> - <?php echo $rootCategory->getName() ?>"
           title="<?php echo $category->getName() ?> - <?php echo $rootCategory->getName() ?>" width="160"
           height="160"/></a>
  </div>

  <h2><a href="<?php echo $category->getLink() ?>" class="underline"><?php echo $category->getName() ?></a></h2>
  <ul>
    <?php /* foreach ($category['links'] as $link): ?>
    <li><a href="<?php echo $link->getLink() ?>"><?php echo $link->getName() ?></a></li>
    <?php endforeach */ ?>
  </ul>
  <div class="font11">
    <a href="<?php echo $category->getLink() ?>"
       class="underline gray"><?php echo $category->getProductCount() ?> товаров</a>
  </div>

  <!-- Hover -->
  <div class="boxhover">
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $category->getLink() ?>">

        <div class="photo">
          <a href="<?php echo $category->getLink() ?>"><!--<i class="new" title="Новинка"></i>--><img
            src="<?php echo $category->getMediaImageUrl() ?>"
            alt="<?php echo $category->getName() ?> - <?php echo $rootCategory->getName() ?>"
            title="<?php echo $category->getName() ?> - <?php echo $rootCategory->getName() ?>" width="160"
            height="160"/></a>
        </div>
        <h2><a href="<?php echo $category->getLink() ?>" class="underline"><?php echo $category->getName() ?></a></h2>
        <ul>
          <?php /* foreach ($category['links'] as $link): ?>
          <li><a href="<?php echo $link->getLink() ?>"><?php echo $link->getName() ?></a></li>
          <?php endforeach */?>
        </ul>
        <div class="font11"><a href="<?php echo $category->getLink() ?>"
                               class="underline gray"><?php echo $category->getProductCount() ?> товаров</a></div>

      </div>
    </div>

  </div>
  <!-- /Hover -->
</div>
