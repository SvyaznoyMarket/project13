<?php if ($userData['photo']): ?> <div class="pb10"><span class="avatar"><b></b><img src="<?php echo $userData['photo']?>" alt="" width="54" height="54" /></span></div> <?php endif; ?>
<div class="font16 pb5">Привет,<br /><strong><?php echo $user->getName()?></strong></div>
<div class="pb10"><?php echo $user->getEmail()?><br /><?php echo $user->getPhonenumber()?></div>
<div class="pb20">
<?php if ($userData['birthday']): ?> Дата рождения: <?php echo $userData['birthday']?><br /> <?php endif; ?>
<?php if ($userData['occupation']): ?>Деятельность: <?php echo $userData['occupation']?> <?php endif; ?>
</div>
