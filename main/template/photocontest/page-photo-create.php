<?php
/**
 * @var $userEntity \Model\User\Entity
 */
?>

<div class="bBuyingInfo">
	<?=\App::closureTemplating()->render('/__breadcrumbs', ['links' => $breadcrumbs]) ?>
	<h1>Стань участником</h1>
	
	<?php if(isset($message)): ?>
	<p class="pc_info"><?=$message?></p>
	<?php endif; ?>

	<?php if(isset($form)): ?>
	<form method="post" action="" enctype="multipart/form-data" class="pc_form">
		<div class="bBuyingLine mBuyingFields clearfix">

			<label class="bBuyingLine__eLeft" for=""><?=$form->title->title?></label>
			<div class="bBuyingLine__eRight">
				<span class="bFeildStarImg">*</span>
				<?php if(isset($form->title->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->title->error?></div></div>
				<?php endif; ?>
				<input type="text" value="<?=$form->title->value?>" name="title" class="bBuyingLine__eText mInputLong">
			</div>
			
			<label class="bBuyingLine__eLeft" for=""><?=$form->orderIds->title?></label>
			<div class="bBuyingLine__eRight">
				<span class="bFeildStarImg">*</span>
				<?php if(isset($form->orderIds->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->orderIds->error?></div></div>
				<?php endif; ?>
				<input type="text" value="<?=$form->orderIds->value?>" name="orderIds" class="bBuyingLine__eText mInputLong">
			</div>

			<label class="bBuyingLine__eLeft" for=""><?=$form->file->title?></label>
			<div class="bBuyingLine__eRight">
				<span class="bFeildStarImg">*</span>
				<?php if(isset($form->file->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->file->error?></div></div>
				<?php endif; ?>
				<input type="file" name="file" class="bBuyingLine__eText mInputLong">
			</div>
			
			
			<?php if(isset($form->email)):?>
			<label class="bBuyingLine__eLeft" for=""><?=$form->email->title?></label>
			<div class="bBuyingLine__eRight">
				<span class="bFeildStarImg">*</span>
				<?php if(isset($form->email->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->email->error?></div></div>
				<?php endif; ?>
				<input type="text" value="<?=$form->email->value?>" name="email" class="bBuyingLine__eText mInputLong mInput265">
			</div>
			<?php endif; ?>

			<?php if(isset($form->mobile)):?>
			<label class="bBuyingLine__eLeft" for=""><?=$form->mobile->title?></label>
			<div class="bBuyingLine__eRight mPhone">
				<span class="bFeildStarImg">*</span>
				<span class="bPlaceholder">+7</span> 
				<?php if(isset($form->mobile->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->mobile->error?></div></div>
				<?php endif; ?>
				<input type="text" value="<?=$form->mobile->value?>" name="mobile" class="bBuyingLine__eText mInputLong" id="order_recipient_phonenumbers">
			</div>
			<?php endif; ?>
			
			<div class="bBuyingLine mConfirm clearfix">
				<div class="bBuyingLine__eLeft"></div>

				<input type="submit" value="Участвовать"/>
			</div>
		</div>
	</form>
	<?php endif; ?>
</div>