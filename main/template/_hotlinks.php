<? if(!empty($hotlinks)): ?>
  <h3>Популярные метки</h3>
  <div class="hotlinks mb15 ml10">
    <div class="totlinksWrapper">
      <? foreach ($hotlinks as $key => $hotlink): ?>
        <a href="<?= $hotlink['url'] ?>" class="mr10<?= $key > 9 ? ' toHide hf' : '' ?>"><?= $hotlink['title'] ?></a>
      <? endforeach ?>
    </div>
    <? if(count($hotlinks) > 10): ?>
      <div class="mt5"><span class="hotlinksToggle link mt5">Все метки</span></div>
    <? endif ?>
  </div>
<? endif ?>