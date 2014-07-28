<?php

return function (
    \Helper\TemplateHelper $helper,
    array $bonusCards = [],
    array $bonusCardsData = []
) {
    if (!empty($bonusCards)):

        $activeCard = reset($bonusCards); ?>

        <label class="bBuyingLine__eLeft">Карта программы лояльности</label>

        <div class="bBuyingLine__eRight jsBonusCard" data-value="<?= $helper->json($bonusCardsData) ?>" data-sclub-id="<?= \Model\Order\BonusCard\Entity::SVYAZNOY_ID ?>" data-sclub-edit-url="<?= \App::router()->generate('user.edit.sclubNumber') ?>">
            <ul class="bSaleList bInputList clearfix">
                <? $i=1; foreach ($bonusCards as $card): ?>
                    <? if (!$card instanceof \Model\Order\BonusCard\Entity) continue ?>

                    <li class="bSaleList__eItem">
                        <input value="<?= $card->getId() ?>" class="jsCustomRadio bCustomInput mCustomRadioBig jsCard" type="radio" id="card_id_<?= $card->getId() ?>" name="order[bonus_card_id]" <?= 1===$i ? 'checked="checked"' : ''?> />
                        <label class="bCustomLabel mCustomLabelRadioBig" for="card_id_<?= $card->getId() ?>"><?= $card->getName() ?></label>
                    </li>
                <? $i++; endforeach ?>
            </ul>

            <div class="jsCardMessage"></div>

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