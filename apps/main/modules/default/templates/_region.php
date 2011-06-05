<div class="block region left">
<?php
$geoip = $sf_request->getParameter('geoip');
echo "Код страны: " . $geoip['country_code'] . "\n";
echo "Регион:     " . $geoip['region'] . "\n";
echo "Имя страны: " . $geoip['country_name'] . "\n";
echo "Город:      " . $geoip['city_name'] . "\n";
?>
</div>