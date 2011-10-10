<?php
/** @var myDoctrinePager */
$pager;

$list  = $pager->getLinks();
$page  = $pager->getPage();
$first = $pager->getFirstPage();
$last  = $pager->getLastPage();
?>

<!-- Pageslist -->
<div class="pageslist <?php echo $pos=='bottom' ? 'fr' : ''?>">
	<span>Страницы:</span>
	<ul>
		<?php if ($page > $first + 2): ?>
			<li class="next"><a href="<?php echo pager_url_for($first) ?>"><?php echo $first ?>...</a></li>
		<?php endif ?>

		<?php foreach ($list as $item): ?>
			<?php if ($item == $page): ?>
				<li class="current"><a href="javascript:void(0)"><?php echo $item ?></a></li>

			<?php elseif ($item >= $page - 2 && $item <= $page + 2): ?>
				<li><a href="<?php echo pager_url_for($item) ?>"><?php echo $item ?></a></li>

			<?php endif ?>
		<?php endforeach ?>

		<?php if ($page < $last - 2): ?>
			<li class="next"><a href="<?php echo pager_url_for($last) ?>">...<?php echo $last ?></a></li>
		<?php endif ?>
    </ul>
</div>
<!-- /Pageslist -->