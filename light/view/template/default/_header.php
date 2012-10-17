<?php
require_once(\light\Config::get('rootPath') . 'lib/LastModifiedHandler.php');
?>

<!-- Topbar -->
<div class="topbar">
  <div class="bRegion">
    <a href="<?php echo $this->url('region.change', array('region' => \light\App::getCurrentUser()->getRegion()->getId())) ?>" id="jsregion" data-url="<?php echo $this->url('region.init') ?>"><?php echo \light\App::getCurrentUser()->getRegion()->getName() ?></a>
    <b>Контакт-сENTER 8 (800) 700-00-09</b>

    <?php if (\light\Config::get('onlineCallEnabled')): ?>
      <a class="bCall" onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62">Позвонить онлайн</a>
    <?php endif ?>

    <a href="<?php echo $this->url('shop.regionList') ?>">Магазины Enter</a>
  </div>

    <noindex>
      <div class="usermenu">
        <?php echo $this->renderFile('default/_user'); ?>
        <a href="<?php echo $this->url('cart.index') ?>" class="hBasket ml10">Моя корзина <span id="topBasket"></span></a>
      </div>
    </noindex>
</div>
<!-- /Topbar -->

<!-- Header -->
<div id="header" class="topmenu">
  <?php LastModifiedHandler::setLastModified();  ?>
  <a id="topLogo" href="/">Enter Связной</a>
  <?php //include_partial('default/logo') ?>
  <?php foreach ($categoryRootList as $item): ?>
  <a id="topmenu-root-<?php echo $item->getId() ?>" class="bToplink"
     title="<?php echo $item->getName() ?>"
     href="<?php echo $item->getLink() ?>"></a>
  <?php endforeach ?>
</div>
<!-- /Header -->
