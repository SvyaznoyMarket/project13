<div class="topbar_l">
    <a class="topbar_loc jsChangeRegion" href="<?= $page->url('region.change', ['regionId' => $user->getRegion()->getId()]) ?>">
        <?= $user->getRegion()->getName() ?>
    </a>

    <div class="topbar_call" itemscope itemtype="http://schema.org/Organization">

        <? if (\App::config()->onlineCall['enabled']): ?>
            <a onclick="window.open(this.href+'?referrer='+escape(window.location.href), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62"><div class="topbar_call_t">Звонок с сайта</div></a>
        <? endif ?>

        <div class="topbar_call_lst">
            <? if (108136 == $user->getRegion()->getId()): // Санкт-Петербург ?>
                <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['spbPhone'] ?></span>
                <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['phone'] ?></span>
            <? elseif (14974 == $user->getRegion()->getId()): // Москва ?>
                <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['moscowPhone'] ?></span>
                <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['phone'] ?></span>
            <? else: ?>
                <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['phone'] ?></span>
                <span class="topbar_call_i" itemprop="telephone"><?= \App::config()->company['moscowPhone'] ?></span>
            <? endif ?>
        </div>
    </div>

    <div class="topbar_lks">
        <a class="topbar_lks_i" href="<?= $page->url('shop') ?>">Магазины Enter</a>
        <a class="topbar_lks_i" href="/how_get_order">Доставка</a>
    </div>
</div>

<div class="topbar_c">
    <a class="topbar_ep" href="<?= \App::router()->generate('enterprize', ['from' => 'enterprize_header']) ?>"><span class="topbar_ep_mark">Enter</span> Prize</a>
</div>