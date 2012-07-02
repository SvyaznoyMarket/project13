<?php
namespace light;

/**
 * @var $rootCategoryList CategoryShortData[]
 * @var $this HtmlRenderer
 */

foreach ($rootCategoryList as $category): ?>
<a id="topmenu-root-<?php echo $category->getId() ?>" title="<?php echo $category->getName() ?>"
   alt="<?php echo $category->getName() ?>" class="bToplink"
   href="<?php echo $this->url('catalog.showCategory', array('categoryToken' => $category->getLink())) ?>">
    <span class="category-<?php echo $category->getId() ?>">
    </span>
</a>
<?php endforeach ?>
