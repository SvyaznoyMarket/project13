<?php
/**
 * Данная конфигурация предназначена для массовой проверси страниц сайта на доступность. Например, для проверки, что на
 * сайте нет ссылок на страницы товаров, возвращающие 404 (из-за чего сайт может забанить яндекс маркет).
 */

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config-live.php';

$c->product['deliveryCalc'] = false;

return $c;