<?php foreach (array('id', 'name', 'latitude', 'longitude') as $i): ?>
  <input type="hidden" name="shop[<?php echo $i ?>]" value="<?php echo $item[$i] ?>" />
<?php endforeach ?>


<!-- bMap -->

<div class="bMap">
  <div id="map-container" class="bMap__eBody" style="width: <?php echo count($item['photos']) ? '787' : '897' ?>px; height: 334px; background: transparent url('/images/loader.gif') no-repeat 50% 50%;"></div>

  <?php if (count($item['photos'])): ?>
  <div class="bMap__eImages">
    <h3 class="bMap__eImagesTitle">Фотогалерея:</h3>

    <?php foreach ($item['photos'] as $photo): ?>
    <div class="bMap__eContainer">
      <img class="bMap__eImgSmall" src="<?php echo $photo['url_small'] ?>">
      <img class="bMap__eImgBig" src="<?php echo $photo['url_big'] ?>">
    </div>
    <?php endforeach ?>

  </div>
  <?php endif ?>

</div>
<!-- /bMap -->


<!-- bMapInfo -->
<div class='bMapInfo'>
  <div class='bMapInfo__eIco mShop'></div>
  <div class='bMapInfo__eText'>
    <h2 class='bMapInfo__eTitle'><?php echo $item['address'] ?></h2>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mBig'>
        <!-- М. Белорусская<br> -->

        <?php if ($item['regime']): ?>
        Работаем <?php echo $item['regime'] ?><br />
        <?php endif ?>
        <?php if ($item['phonenumbers']): ?>
        Телефон<?php echo false !== strpos($item['phonenumbers'], ',') ? 'ы:' : ':' ?>: <?php echo $item['phonenumbers'] ?><br />
        <?php endif ?>
      </span>
    </p>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>
        <?php echo $item['description'] ?>
      </span>
    </p>

  </div>
</div>
<!-- /bMapInfo -->

<!-- bMapInfo -->
<div class='bMapInfo'>
  <div class='bMapInfo__eIco mCar'></div>
  <div class='bMapInfo__eText'>
    <h2 class='bMapInfo__eTitle'>Как добраться</h2>

    <?php if ($item['way_walk']): ?>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>
        <b>Проезд на городском транспорте</b><br>
        <?php echo $item['way_walk'] ?>
      </span>
    </p>
    <?php endif ?>

    <?php if ($item['way_auto']): ?>
    <p class='bMapInfo__eP'>
      <span class='bMapInfo__eSpan mSmall'>

        <b>Проезд на авто</b><br>
        <?php echo $item['way_auto'] ?>
      </span>
    </p>
    <?php endif ?>

  </div>
</div>
<!-- /bMapInfo -->

<div class="clear"></div>

