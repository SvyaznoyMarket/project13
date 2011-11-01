<?php if (0 == $quantity): ?>
<b class="supply4"></b>Звоните

<?php elseif (($quantity > 0) && ($quantity < 4)): ?>
<b class="supply3"></b>Мало

<?php elseif (($quantity > 4) && ($quantity < 20)): ?>
<b class="supply1"></b>Много

<?php elseif ($quantity > 20): ?>
<b class="supply2"></b>Очень много

<?php endif ?>
