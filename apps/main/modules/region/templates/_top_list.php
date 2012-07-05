<?php foreach ($regions as $region): ?>
<div class="bCityPopup__eBlock">
  <a href="<?php echo url_for('region_change', array('region' => $region['core_id'])) ?>"><?php echo $region['name'] ?></a>
</div>
<?php endforeach ?>