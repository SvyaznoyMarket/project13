<?php foreach ($list as $item): /** @var $item ProductCategoryEntity */ ?>
<a id="topmenu-root-<?php echo $item->getId() ?>" title="<?php echo $item->getName() ?>"
   alt="<?php echo $item->getName() ?>" class="bToplink"
   href="<?php echo $item->getLink() ?>">
    <span class="category-<?php echo $item->getId() ?>">
    </span>
</a>
<?php endforeach ?>
