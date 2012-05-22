<!-- bMapShops -->
<div class="bMapShops">
  <div class='bMapShops__eHead'>
    <div class='bMapShops__eRegion'>
      <h2 class='bMapShops__eRegionTitle'>Enter в <?php echo $region->getLinguisticCase('п') ? $region->getLinguisticCase('п') : ($region->prefix.$region) ?>!</h2>
      <div class='bMapShops__eRegionText'>Enter в регионах:</div>
      <div class="selectbox selectbox170 fl"><i></i>
        <select id="region-select" class="styled" name="region">
        <?php foreach ($regionList as $record): ?>
          <option data-url="<?php echo url_for('shop', array('region' => $record->token)) ?>" <?php if ($record->id == $region->id) echo 'selected="selected"' ?> value="<?php echo $record['id']?>"><?php echo $record['name'] ?></option>
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

<input id="map-center" type="hidden" data-content='<?php echo json_encode(array('latitude' => $region['latitude'], 'longitude' => $region['longitude'])) ?>' />
<input id="map-markers" type="hidden" data-content='<?php echo json_encode($markers) ?>' />
<!-- /bMapShops -->

<div id="map-info_window-container" style="display:none"></div>

<script type="text/html" id="infowindowtmpl">
	<div class="bMapShops__ePopupRel">
		<h3><%=address%></h3>
		<span><%=regtime%></span><br>
		<a href="<%=link%>" class="bGrayButton shopchoose">Перейти к магазину</a>
	</div>
</script>