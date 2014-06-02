<?php
/**
 * @var $page               \View\DefaultLayout
 * @var $gridCells          \Model\GridCell\Entity[]
 * @var $category           \Model\Product\Category\Entity
 * @var $catalogConfig      array
 * @var $rootCategoryInMenu \Model\Product\Category\TreeEntity|null
 * @var $productsById       \Model\Product\CompactEntity[]
 */
?>

<?
$helper = new \Helper\TemplateHelper();

$siblingCategories = $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : [];
?>

<?//= $helper->render('product-category/__breadcrumbs', ['category' => $category]) // хлебные крошки ?>

<? if ((bool)$siblingCategories): ?>
    <?= $helper->render('product-category/__sibling-list', [
        'categories'         => $siblingCategories,
        'catalogConfig'      => $catalogConfig,
        'currentCategory'    => $category,
        'rootCategoryInMenu' => $rootCategoryInMenu
    ]) // категории-соседи ?>
<? endif ?>

<? if (false): ?>
    <h1 class="bTitlePage"><?= $category->getName() ?></h1>
<? endif ?>

<? if (false): ?>
<!-- TCHIBO - навигация по разделу Чибо -->
<div class="tchiboNavSection">
	<img class="tchiboNavSection__img" src="/styles/tchiboNavSection/img/menuSecImg.jpg" />

	<h2 class="tchiboNavSection__title">Мечта мужчин</h2><!--/ название категории -->

	<!-- список подкатегорий -->
	<ul class="tchiboNavSection__list">
		<li class="item">
			<a class="link mActive" href="">Мечта мужчин</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div><!--/ картинка при наведении -->
		</li>

		<li class="item">
			<a class="link" href="">Классика</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Мужской сезон</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Коллекция с длинным-предлинным названием</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">100% натуральное</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Ещё коллекция</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">Какая-то ещё коллекция</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>

		<li class="item">
			<a class="link" href="">100% натуральное</a>

			<div class="itemHover">
				<img class="itemHover__img" src="/styles/tchiboNavSection/img/hoverImg.jpg" />
				<div class="itemHover__border"></div>
			</div>
		</li>
	</ul>
	<!--/ список подкатегорий -->
</div>
<!-- TCHIBO - навигация по разделу Чибо -->

<div class="tchiboNavSec">
	<ul class="tchiboNavSec__list">
		<li class="item"><a class="link mActive" href="">Классика</a></li>
		<li class="item"><a class="link" href="">Коллекция с длинным-предлинным названием</a></li>
		<li class="item"><a class="link" href="">100% натуральное</a></li>
		<li class="item"><a class="link" href="">Какая-то ещё коллекция</a></li>
		<li class="item"><a class="link" href="">Классика</a></li>
		<li class="item"><a class="link" href="">Коллекция с длинным-предлинным названием</a></li>
		<li class="item"><a class="link" href="">100% натуральное</a></li>
		<li class="item"><a class="link" href="">Какая-то ещё коллекция</a></li>
	</ul>
</div>

<? endif ?>


<?
$config = \App::config()->tchibo;

$contentHeight = 0;
foreach ($gridCells as $cell) {
    $height =
        (($cell->getRow() - 1) *  $config['rowWidth'] + ($cell->getRow() - 1) * $config['rowPadding'])
        + ($cell->getSizeY() * $config['rowHeight'] + ($cell->getSizeY() - 1) * $config['rowPadding']);
    if ($height > $contentHeight) {
        $contentHeight = $height;
    }
}
?>
<!-- TCHIBO - листинг Чибо -->
<div class="tchiboProducts" style="position: relative; height: <?= $contentHeight ?>px; margin: 0 0 10px 7px;">
<?= $helper->render('grid/__show', [
    'gridCells'    => $gridCells,
    'productsByUi' => $productsByUi,
]) ?>
</div>
<!--/ TCHIBO - листинг Чибо -->