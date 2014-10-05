<div class="topbar_l">
    <a class="topbar_loc jsChangeRegion" href="<?= $page->url('region.change', ['regionId' => $user->getRegion()->getId()]) ?>" data-url="<?= $page->url('region.init') ?>" data-region-id="<?= $user->getRegion()->getId() ?>" data-autoresolve-url="<?= $page->url('region.autoresolve', ['nocache' => 1]) ?>">
        <?= ((mb_strlen($user->getRegion()->getName()) > 20) ? (mb_substr($user->getRegion()->getName(), 0, 20) . '...') : $user->getRegion()->getName()) ?>
    </a>

    <div class="topbar_call" itemscope itemtype="http://schema.org/Organization">

    <? if (\App::config()->onlineCall['enabled']): ?>
        <a onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62"><div class="topbar_call_t">Звонок с сайта</div></a>
    <? endif ?>

        <div class="topbar_call_lst">
            <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['phone'] ?></span>
            <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['moscowPhone'] ?></span>
        </div>
    </div>

    <div class="topbar_lks">
        <a class="topbar_lks_i" href="<?= $page->url('shop') ?>">Магазины Enter</a>
        <a class="topbar_lks_i" href="/how_get_order">Доставка</a>
    </div>
</div>

<div class="topbar_c">
    <a class="topbar_ep" href="<?= \App::router()->generate('enterprize') ?>"><span class="topbar_ep_mark">Enter</span> Prize</a>
</div>