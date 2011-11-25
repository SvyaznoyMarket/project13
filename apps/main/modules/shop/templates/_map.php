<!-- bMapShops -->
<div class="bMapShops">
  <div class='bMapShops__eHead'>
    <div class='bMapShops__eRegion'>
      <h2 class='bMapShops__eRegionTitle'>Enter в <?php echo $region->getLinguisticCase('п') ? $region->getLinguisticCase('п') : ($region->prefix.$region) ?>!</h2>
      <div class='bMapShops__eRegionText'>Enter в регионах:</div>
      <div class="selectbox selectbox170 fl"><i></i>
        <select id="region-select" class="styled" name="region">
        <?php foreach ($regionList as $region): ?>
          <option value="<?php echo $region['id']?>"><?php echo $region['name'] ?></option>
        <?php endforeach ?>
        </select>
      </div>
    </div>
    <img class='bMapShops__eImage' src='<?php echo image_path('shop-h1.png') ?>'>
  </div>
  <div class='bMapShops__eContent'>

    <h2 class='bMapShops__eTitle'>Магазины Enter на карте</h2>
    <div id="region_map-container" class='bMapShops__eMapWrap' style="width: 880px; height: 490px;"></div>
  </div>
</div>

<input id="map-centers" type="hidden" data-content='<?php echo json_encode($regions) ?>' />
<input id="map-markers" type="hidden" data-content='<?php echo json_encode($markers) ?>' />
<!-- /bMapShops -->

<div id="map-info_window-container" class="hf">
  <div class="bigmark">
    <b class="corner"></b>
    <div>
      <h2 class="title" data-name="name"></h2>
      <span data-name="link"></span>
      <!--
      <span data-name="regime" data-title="Работаем"></span>
      <span data-name="phonenumbers" data-title="Телефон:"></span>
      -->
    </div>
  </div>
</div>