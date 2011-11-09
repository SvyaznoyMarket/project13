<?php foreach (array('id', 'name', 'latitude', 'longitude') as $i): ?>
<input type="hidden" name="shop[<?php echo $i ?>]" value="<?php echo $item[$i] ?>" />
<?php endforeach ?>


<div id="map-container" class="mapbox" style="width:920px; height:332px; background: transparent url('/images/loader.gif') no-repeat 50% 50%;"></div>

<div class="fl width450 lh15">
  <div class="font16 pb10"><strong>Адреса и контакты:</strong></div>
  <?php echo $item['address'] ?><br />

  <?php if ($item['regime']): ?>
  Работаем <?php echo $item['regime'] ?><br />
  <?php endif ?>

  <?php if ($item['phonenumbers']): ?>
  Телефон<?php echo false !== strpos($item['phonenumbers'], ',') ? 'ы' : '' ?>: <?php echo $item['phonenumbers'] ?><br />
  <?php endif ?>

  <br />

  <div class="font16 pb10"><strong>Как добраться:</strong></div>

  <?php if ($item['way_walk']): ?>
  <div class="pb5"><strong>Проезд на городском транспорте</strong></div>
  <?php echo $item['way_walk'] ?>
  <br />
  <?php endif ?>

  <?php if ($item['way_auto']): ?>
  <div class="pb5"><strong>Проезд на авто</strong></div>
  <?php echo $item['way_auto'] ?>
  <?php endif ?>

</div>

<div class="fr width450">
  <div class="font16 pb10"><strong>О магазине:</strong></div>
  <?php echo $item['description'] ?>
</div>

<div class="clear"></div>

