<?php
/**
 * @var $page               \View\Service\IndexPage
 * @var $categories         \Model\Product\Service\Category\Entity[]
 * @var $category           \Model\Product\Service\Category\Entity
 * @var $servicesByCategory \Model\Product\Service\Entity[]
 * @var $service            \Model\Product\Service\Entity
 */
?>


<div class="line pb20 mt32"></div>
<? foreach ($category->getChild() as $i => $child): ?>
    <? if ($i > 0): ?>
        <div class="line pb20 mt32"></div>
    <? endif ?>

    <div class="serviceblock mNewSB">
        <div class="photo pr">
            <? if ($child->getImage()): ?>
                <div class="bServiceCard__eLogo"></div>
                <img class='bF1ServiceImg' alt="<?= $child->getName()?>" src="<?= $child->getImageUrl() ?>" />
            <? else: ?>
                <img alt="<?= $child->getName()?>" src="/images/f1infobig.png" />
            <? endif ?>
        </div>
        <div class="info">
            <h3><?= $child->getName() ?></h3>

            <div class="hf">
                <? if (isset($servicesByCategory[$child->getId()])) foreach ($servicesByCategory[$child->getId()] as $service): ?>
                    <div class="font16 pb8">
                        <a href="<?= $page->url('service.show', array('serviceToken' => $service->getToken())) ?>">
                            <?= $service->getName() ?>
                        </a>
                        <? if (!is_null($service->getPrice())): ?>
                            <div class="font16 mInlineBlock">
                            <? if ($service->getPrice()): ?>
                                &mdash; <strong><?= $page->helper->formatPrice($service->getPrice()) ?>&nbsp;<span class="rubl">p</span></strong>
                            <? else: ?>
                                &mdash; <strong>бесплатно</strong>
                            <? endif ?>
                            </div>
                        <? elseif (!is_null($service->getPriceMin())): ?>
                            <div class="font16 mInlineBlock">
                                &mdash; от <strong><?= $page->helper->formatPrice($service->getPriceMin()) ?>&nbsp;<span class="rubl">p</span></strong>
                            </div>
                        <? endif ?>
                    </div>
                    <div class="pb20">
                        <?= $service->getDescription() ?>
                        <?= $service->getWork() ?>
                    </div>
                <? endforeach ?>
            </div>
        </div>
    </div>
<? endforeach ?>
