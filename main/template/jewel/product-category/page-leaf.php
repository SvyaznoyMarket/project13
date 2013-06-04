<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $productVideosByProduct array
 */
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>
<div class="clear"></div>

<? if(!empty($promoContent)): ?>
    <?= $promoContent ?>
<? endif ?>

<div class="clear"></div>
<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>


<script>
  $(document).ready(function(){
    $(".items-section__list .item").hover(
      function() {
      $(this).addClass('hover');
    },
      function() {
      $(this).removeClass('hover');
    });
  });
</script>

<div class="logo-section" style="background: url('/css/pandoraCatalog/img/pandora_logo.gif') no-repeat 50% 0;">Ювелирные Украшения</div>

<nav class="brand-nav">
  <ul class="brand-nav__list clearfix">
    <li><a href="">Подвески-шармы</a></li>
    <li class="braslets"><a href="">Браслеты</a></li>
    <li><a href="">Кольца</a></li>
    <li><a href="">Серьги</a></li>
    <li class="kolye_kuloni"><a href="">Колье и<br/>кулоны</a></li>
    <li class="new"><a href="">Новинки</a></li>
  </ul>
</nav>

<div class="banner-categories">
  <img class="banner-categories__img" src="/css/pandoraCatalog/img/pandora_catalog_banner.jpg" alt="" />
  <div class="banner-categories__title">Подвески -<br/>шармы</div>
</div>

<nav class="brand-subnav clearfix">
  <div class="brand-subnav__title">Подвески - шармы</div>
  <ul class="brand-subnav__list">
    <li><a href="">Все</a></li>
    <li><a href="">Подвески-шармы</a></li>
    <li><a class="active" href="">Красивости</a></li>
    <li><a href="">Няшеньки</a></li>
    <li><a href="">Украшения</a></li>
    <li><a href="">Соединительные цепочки</a></li>
  </ul>
</nav>

<div class="filter-section">
  <ul class="clearfix">
    <li>
      <div class="filter-section__title">Металлы</div>
      <div class="filter-section__value">
        <a href="">Все металлы</a>
        <ul class="filter-section__value__dd">
          <li><a href="">золото</a></li>
          <li><a href="">серебро</a></li>
          <li><a href="">бронза</a></li>
          <li><a href="">медь</a></li>
        </ul>
      </div>
    </li>
    <li>
      <div class="filter-section__title">Металлы</div>
      <div class="filter-section__value">
        <a href="">Все металлы</a>
        <ul class="filter-section__value__dd">
          <li><a href="">золото</a></li>
          <li><a href="">серебро</a></li>
          <li><a href="">бронза</a></li>
          <li><a href="">медь</a></li>
        </ul>
      </div>
    </li>
    <li>
      <div class="filter-section__title">Металлы</div>
      <div class="filter-section__value">
        <a href="">Все металлы</a>
        <ul class="filter-section__value__dd">
          <li><a href="">золото</a></li>
          <li><a href="">серебро</a></li>
          <li><a href="">бронза</a></li>
          <li><a href="">медь</a></li>
        </ul>
      </div>
    </li>
    <li>
      <div class="filter-section__title">Металлы</div>
      <div class="filter-section__value">
        <a href="">Все металлы</a>
        <ul class="filter-section__value__dd">
          <li><a href="">золото</a></li>
          <li><a href="">серебро</a></li>
          <li><a href="">бронза</a></li>
          <li><a href="">медь</a></li>
        </ul>
      </div>
    </li>
    <li class="last">
      <div class="filter-section__title">Металлы</div>
      <div class="filter-section__value">
        <a href="">Все металлы</a>
        <ul class="filter-section__value__dd">
          <li><a href="">золото</a></li>
          <li><a href="">серебро</a></li>
          <li><a href="">бронза</a></li>
          <li><a href="">медь</a></li>
        </ul>
      </div>
    </li>
  </ul>
</div>


<?= $page->render('jewel/product/_pager', array(
    'request'                => $request,
    'pager'                  => $productPager,
    'productFilter'          => $productFilter,
    'productSorting'         => $productSorting,
    'hasListView'            => true,
    'category'               => $category,
    'view'                   => $productView,
    'productVideosByProduct' => $productVideosByProduct,
    'itemsPerRow'            => $itemsPerRow,
)) ?>
