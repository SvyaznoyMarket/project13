<!-- Topbar -->
<div class="topbar">
  <div class="bRegion">
    <a href="<?php echo url_for('region_change', array('region' => $sf_user->getRegion('core_id'))) ?>" id="jsregion" data-url="<?php echo url_for('region_init') ?>"><?php echo $sf_user->getRegion('name') ?></a>
    <b>Контакт-cENTER 8 (800) 700-00-09</b>

    <?php if (sfConfig::get('app_online_call_enabled')): ?>
      <a class="bCall" onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62">Позвонить онлайн</a>
    <?php endif ?>

    <a href="<?php echo url_for('shop') ?>">Магазины Enter</a>
  </div>
  <noindex>
	  <div class="usermenu">
	    <?php include_partial('default/user') ?>
	    <a href="<?php echo url_for('cart') ?>" class="hBasket ml10">Моя корзина <span id="topBasket"></span></a>
	  </div>
	</noindex>
</div>
<!-- /Topbar -->

<!-- Header -->
<div id="header" class="topmenu">
    <?php LastModifiedHandler::setLastModified();  ?>
    <a id="topLogo" href="/">Enter Связной</a>
    <?php //include_partial('default/logo') ?>
    <?php include_component('productCategory_', 'root_list') ?>
</div>
<!-- /Header -->
