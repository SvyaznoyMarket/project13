<?= $htmlContent ?>

<div id="contentPageData" class="hf" data-data="<?= $page->json($data) ?>"></div>

<? if (in_array($token, ['service_ha', 'services_ha'])) { ?>
  <?= $page->render('content/_serviceHa', ['serviceJson' => $data]) ?>
<? } ?>

<div class="pb20"></div>
