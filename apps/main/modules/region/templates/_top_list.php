<?php foreach ($regions as $region): /** @var $region RegionEntity */?>
<div class="bCityPopup__eBlock">
  <a href="<?php echo url_for('region_change', array('region' => $region->getId())) ?>"><?php echo $region->getName() ?></a>
</div>
<?php endforeach ?>