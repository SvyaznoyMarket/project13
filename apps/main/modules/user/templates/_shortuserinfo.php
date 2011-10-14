<?php if ($userData['photo']): ?> <div class="pb10"><span class="avatar"><b></b><img src="<?=$userData['photo']?>" alt="" width="54" height="54" /></span></div> <?php endif; ?>
<div class="font16 pb5">Привет,<br /><strong><?=$user->getName()?></strong></div>
<div class="pb10"><?=$user->getEmail()?><br /><?=$user->getPhonenumber()?></div> 
<div class="pb20">
<?php if ($userData['birthday']): ?> Дата рождения: <?=$userData['birthday']?><br /> <?php endif; ?>
<?php if ($userData['occupation']): ?>Деятельность: <?=$userData['occupation']?> <?php endif; ?>
</div>
