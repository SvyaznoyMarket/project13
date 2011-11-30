<div><b>Похожие услуги</b></div>
<?php
foreach ($list as $service) {
    echo '<div><a href="' .url_for('service_show', array('service' => $service['token'])) . '" >' . $service['name'] . '</a></div>';
}
?>
