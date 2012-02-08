<div class='bInShop__eArticul'>Артикул #<?php echo $item['article'] ?></div>
<div class='bInShop__eImage'><img src="<?php echo $item['photo'] ?>" alt="" width="160" height="160" title="" /></div>

<div class='bInShop__ePrice'><span class="price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></div>
<div class='bInShop__eButton'><a href="#" class='bOrangeButton<?php if (!$item['is_instock']) echo ' disable' ?>'><i></i> Купить с доставкой</a></div>
<p class='bInShop__eDescription'><?php echo $item['description'] ?></p>
<div class='bInShop__eButton'><a href="<?php echo $item['url'] ?>" class='bWhiteButton'>Подробнее о товаре</a></div>