<?php

$brands = [
    ['name' => 'Bosch', 'url' => '/slices/brands-bosch', 'image' => 'bosch.jpg'],
    ['name' => 'LG', 'url' => '/slices/brands-lg', 'image' => 'lg.jpg'],
    ['name' => 'Samsung', 'url' => '/slices/brands-samsung', 'image' => 'samsung.jpg'],
    ['name' => 'Philips', 'url' => '/slices/brands-philips', 'image' => 'philips.jpg'],
    ['name' => 'Electrolux', 'url' => '/slices/brands-electrolux', 'image' => 'electrolux.jpg'],
    ['name' => 'Sony', 'url' => '/slices/brands-sony', 'image' => 'sony.jpg'],
    ['name' => 'Apple', 'url' => '/slices/brands-apple', 'image' => 'apple.jpg'],
    ['name' => 'HP', 'url' => '/slices/brands-hp', 'image' => 'HP.jpg'],
    ['name' => 'Dr.Beckmann', 'url' => '/slices/brands-lenovo', 'image' => 'dr-beckmann.jpg'],
    ['name' => 'Hasbro', 'url' => '/slices/brands-hasbro', 'image' => 'hasHasbrobro.jpg'],
    ['name' => 'Tchibo', 'url' => '/catalog/tchibo', 'image' => 'tchibo.jpg'],
    ['name' => 'LEGO', 'url' => '/slices/brands-lego', 'image' => 'lego.jpg'],
    ['name' => 'Анзоли', 'url' => '/slices/brands-anzoli', 'image' => 'anzoli.jpg'],
    ['name' => 'Шатура', 'url' => '/slices/brands-shatura', 'image' => 'shatura.jpg'],
    ['name' => 'Frosch', 'url' => '/slices/brands-vision', 'image' => 'frosch.jpg'],
    ['name' => 'Makita', 'url' => '/slices/brands-makita', 'image' => 'Makita.jpg'],
    ['name' => 'Аскона', 'url' => '/slices/brands-askona', 'image' => 'askona.jpg'],
    ['name' => 'Tefal', 'url' => '/slices/brands-tefal', 'image' => 'tefal.jpg'],
    ['name' => 'PANDORA', 'url' => '/slices/brands-pandora', 'image' => 'pandora.jpg'],
    ['name' => 'GUESS', 'url' => '/slices/brands-guess', 'image' => 'guess.jpg'],
];

?>
<div class="infoBox">
    <div class="infoBox_tl">
        ПОПУЛЯРНЫЕ БРЕНДЫ
    </div>

    <ul class="lstitem lstitem-10i clearfix">
    <? foreach ($brands as $brand): ?>
        <li class="lstitem_i">
            <a class="lstitem_lk jsMainBrand" title="<?= $brand['name'] ?>" href="<?= $brand['url'] ?>">
                <img src="styles/mainpage/img/logo/<?= $brand['image'] ?>" alt="<?= $brand['name'] ?>" class="lstitem_img">
            </a>
        </li>
    <? endforeach ?>
    </ul>
</div>
