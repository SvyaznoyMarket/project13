<?php if (($quantity > 0) && ($quantity < 5)): ?>
<i class='mIn1'></i> <span>Мало</span>

<?php elseif (($quantity >= 5) && ($quantity < 10)): ?>
<i class='mIn2'></i> <span>Много</span>

<?php elseif ($quantity >= 10): ?>
<i class='mIn3'></i> <span>Очень много</span>

<?php endif ?>
