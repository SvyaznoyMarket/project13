<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory, 'is_root' => true, )) ?>
<?php end_slot() ?>


<div class="advertbox">
  <b class="tl"></b><b class="tr"></b><b class="bl"></b><b class="br"></b>
  <div><img src="/images/images/photo1.jpg" alt="" width="469" height="342" /></div>

  <ul>
    <li class="current"><i></i><a href=""><span>Спланируй что-нибудь!</span>Попробуй спланировать комнату своей мечты!</a></li>
    <li><i></i><a href=""><span>23 варианта кухни для дачи</span>Попробуй спланировать комнату своей мечты!</a></li>
  </ul>
</div>

<div class="clear"></div>

<?php echo include_component('productCategory', 'child_list', array('view' => 'preview', 'productCategory' => $productCategory)) ?>
