<?php
/**
 * @var $item ProductEntity
 */
$last = count($item->getTagList()) - 1;
if ($last > -1):
?>
<noindex>
  <div class="pb25">
    <strong>Теги:</strong>
    <?php foreach ($item->getTagList() as $i => $tag):?>
    <a href="<?php echo $tag->getSiteUrl() ?>" class="underline"
       rel="nofollow"><?php echo $tag->getName() ?></a><?php echo $i < $last ? ', ' : '' ?>
    <?php endforeach ?>
  </div>
</noindex>
<?php endif ?>