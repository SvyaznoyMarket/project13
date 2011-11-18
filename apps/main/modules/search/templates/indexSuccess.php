<?php use_helper('I18N') ?>

<?php slot('title', trim(get_partial('search/product_count', array('count' => $resultCount, 'searchString' => $searchString)))) ?>

<?php slot('navigation') ?>
  <?php include_component('search', 'navigation', array('searchString' => $searchString)) ?>
<?php end_slot() ?>

<?php slot('page_head') ?>
  <?php include_partial('search/page_head') ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('search', 'filter_productType', array('searchString' => $searchString, 'productTypeList' => $productTypeList, 'productType' => $productType)) ?>
  <!--
  <dl class="bCtg">

    <dt class='bCtg__eOrange'>Другие также искали</dt>
    <dd>
      <ul>
        <li><a href><span class='bCtg__eL1'>Ноутбук Apple MacBook Pro</span></a></li>
        <li><a href><span class='bCtg__eL1'>защитная пленка на iPad</span></a></li>
        <li><a href><span class='bCtg__eL1'>Моноблок Apple</span></a></li>
        </li>
      </ul>

    </dd>
  </dl>
  -->
<?php end_slot() ?>

<?php include_partial('productCatalog/product_list', array('productPager' => $pagers['product'], 'noInfinity' => $noInfinity)) ?>
