<!-- Topbar -->
<div class="topbar">
  <div class="bRegion">
    <?php include_component('region', 'select') ?>
    <b>Контакт cENTER 8 (800) 700-00-09</b>
    <a href="<?php echo url_for('shop') ?>">Магазины Enter</a>

    <noindex>
      <div class="usermenu">
        <?php include_partial('default/user') ?>
        <a href="<?php echo url_for('cart') ?>" class="hBasket ml10">Моя корзина <span id="topBasket"></span></a>
      </div>
    </noindex>
  </div>
</div>
<!-- /Topbar -->

<!-- Header -->
<div class="bHeaderWrap">
  <div class="bHeader topmenu">
    <?php LastModifiedHandler::setLastModified();  ?>
    <?php include_partial('default/logo') ?>
    <?php include_component('productCategory', 'root_list') ?>
    <div class="bHeader__eLong"></div>
  </div>
</div>
<!-- /Header -->
