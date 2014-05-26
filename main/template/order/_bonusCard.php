<?php

return function (
    \Helper\TemplateHelper $helper,
    array $bonusCards = [],
    array $bonusCardsData = []
) {
    if (!empty($bonusCards)):

        $activeCard = reset($bonusCards); ?>

        <label class="bBuyingLine__eLeft">Карта программы лояльности</label>

        <div class="bBuyingLine__eRight jsBonusCard" data-value="<?= $helper->json($bonusCardsData) ?>">
            <ul class="bSaleList bInputList clearfix">
                <? $i=1; foreach ($bonusCards as $card): ?>
                    <? if (!$card instanceof \Model\Order\BonusCard\Entity) continue ?>

                    <li class="bSaleList__eItem">
                        <input value="" class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="cupon<?= $i ?>" name="bonus_card" <?= 1===$i ? 'checked="checked"' : ''?> />
                        <label class="bCustomLabel mCustomLabelRadioBig" for="cupon<?= $i ?>"><?= $card->getName() ?></label>
                    </li>
                <? $i++; endforeach ?>
            </ul>

            <? if ($activeCard && $activeCard instanceof \Model\Order\BonusCard\Entity): ?>
                <div class="bBuyingLine__eRight mSClub jsActiveCard"<? if ((bool)$activeCard->getImage()): ?> style="background: url(<?= $activeCard->getImage() ?>) 260px -3px no-repeat"<? endif ?>>
                    <label class="bPlaceholder">Номер</label>
                    <input id="bonus-card-number" type="text" placeholder="<?= $activeCard->getMask() ?>" class="bBuyingLine__eText jsCardNumber" name="order[bonus_card_number]" />
                    <div class="bText jsDescription"><?= $activeCard->getDescription() ?></div>
                </div>
            <? endif ?>
        </div>
    <? endif ?>
<? };