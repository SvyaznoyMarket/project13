<?php
/**
 * @var $page     \View\Service\ShowPage
 * @var $user     \Session\User
 * @var $category \Model\Product\Service\Category\Entity
 * @var $service  \Model\Product\Service\Entity
 * @var $alike    \Model\Product\Service\Entity
 */
?>

<?
$json = array(
    'jsref'   => $service->getToken(),
    'jsimg'   => $service->getImage() ? $service->getImageUrl(2) : '/images/f1infobig.png',
    'jstitle' => $service->getName(),
    'jsprice' => $page->helper->formatPrice($service->getPrice()),
);

?>
<div class="bSet">

    <? if ($service->getImage()): ?>
        <div class="bSet__eImage mServiceBig">
            <div class="bServiceCard__eLogo"></div>
            <img src="<?= $service->getImageUrl(); ?>"/>
        </div>
        <div class="bSet__eInfo">
    <? else: ?>
        <div class="bSet__eImage_small mServiceBig">
            <img alt="" src="/images/f1infobig.png">
        </div>
        <div class="bSet__eInfo_big">
    <? endif ?>

    <p class="bSet__eDescription">
        <?= $service->getDescription() ?>
        <?= $service->getWork(); ?>
    </p>

    <div class="bSet__ePrice mServ" ref="<?= $service->getToken() ?>" data-value="<?= $page->json($json) ?>">
        <? if (!is_null($service->getPrice())): ?>
        <div class="font34">
            <strong>
            <? if ($service->getPrice()): ?>
                <span class="price"><?= $page->helper->formatPrice($service->getPrice()) ?></span> <span class="rubl">p</span>
            <? else: ?>
                <span class="price">бесплатно</span>
            <? endif ?>
            </strong>
        </div>
        <? elseif (!is_null($service->getPricePercent())): ?>
        <div class="font16">
            <strong><?= $service->getPricePercent() ?>%</strong> от стоимости товара<?php if (!is_null($service->getPriceMin())): ?>, но не менее <strong><span class="price"><?= $page->helper->formatPrice($service->getPriceMin()) ?></span> <span class="rubl">p</span></strong><?php endif ?>
        </div>
        <? endif ?>

        <? if ($user->getRegion()->getHasService()): ?>
            <? if ($service->isInSale()): ?>
                <a class="link1 gaEvent"
                   href="<?= $page->url('cart.service.set', array('serviceId' => $service->getId(), 'quantity' => 1, 'productId' => 0)); ?>"
                   data-event="BuyF1" data-title="Заказ услуги F1">Купить услугу</a>
            <? elseif ($service->getIsInShop()): ?>
                <b>Доступна в магазине</b>
                <p class="font14">Специалисты Контакт-сENTER с радостью проконсультируют по данной услуге и подскажут ближайший магазин Enter</p>
                <p class="font14">
                    <strong><?= \App::config()->company['phone'] ?></strong><br/>
                    Skype: skype2enter и call2enter<br/>
                    ICQ: <?= \App::config()->company['icq'] ?>
                </p>
            <? endif ?>
        <? endif ?>
    </div>
</div>
</div>



<? if ((bool)$service->getAlike()): ?>
    <div class="rubrictitle"><h3>А так же есть похожие услуги:</h3></div>
    <div class="line pb15"></div>
    <div class="clear"></div>

    <div class="bServiceCardWrap">

        <? foreach ($service->getAlike() as $alike): ?>
        <div class="bServiceCard mInlineBlock" ref="<?= $alike->getToken() ?>">
            <div class="bServiceCard__eImage">
                <a href="<?= $page->url('service.show', array('serviceToken' => $alike->getToken())) ?>">
                    <? if ($alike->getImage()): ?>
                        <img src="<?= $alike->getImageUrl(2); ?>" alt="<?= $alike->getName()?>" />
                    <? endif ?>
                    <div class="bServiceCard__eLogo_free"></div>
                </a>
            </div>

            <p class="bServiceCard__eDescription mb5">
                <a href="<?= $page->url('service.show', array('serviceToken' => $alike->getToken())) ?>">
                    <?= $alike->getName(); ?>
                </a>
            </p>

            <div class="bServiceCard__ePrice pb10">
                <? if (!is_null($alike->getPrice())): ?>
                    <? if($alike->getPrice()): ?>
                    <?= $page->helper->formatPrice($alike->getPrice()); ?>
                    <span class="rubl">p</span>
                    <? else: ?>
                    бесплатно
                    <? endif ?>
                <? elseif (!is_null($alike->getPriceMin())): ?>
                    от <?= $page->helper->formatPrice($alike->getPriceMin()) ?>
                <span class="rubl">p</span>
                <? elseif ($alike->getIsInShop() && $user->getRegion()->getHasService()): ?>
                    <div>доступна в магазине</div>
                <? endif ?>
            </div>

            <? $json = array('jsref' => $alike->getToken(), 'jsimg' => $alike->getImageUrl(), 'jstitle' => $alike->getName(), 'jsprice' => $page->helper->formatPrice($alike->getPrice()), 'url' => $page->url('cart.service.set', array('serviceId' => $alike->getId(), 'quantity' => 1, 'productId' => 0))) ?>

            <? if ($alike->isInSale() && $user->getRegion()->getHasService()) : ?>
                <form action="<?= $page->url('cart.service.set', array('serviceId' => $alike->getId(), 'quantity' => 1, 'productId' => 0)) ?>">
                    <input data-value="<?= $page->json($json) ?>" data-event="BuyF1" data-title="Заказ услуги F1" type="submit" class="button yellowbutton gaEvent" value="Купить услугу"/>
                </form>
            <? endif ?>
        </div>
        <? endforeach ?>
    </div>
<? endif ?>

<br />