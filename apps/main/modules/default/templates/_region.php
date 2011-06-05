<div class="block region left">
<?php $geoip = $sf_request->getParameter('geoip') ?>
<?php echo "Код страны: " . $geoip['country_code'] ?>
<?php echo "Регион:     " . $geoip['region'] ?>
<?php echo "Имя страны: " . $geoip['country_name'] ?>
<?php echo "Город:      " . $geoip['city_name'] ?>
</div>