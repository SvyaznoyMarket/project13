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

			<?php if(isset($form->name)):?>
			<label class="bBuyingLine__eLeft" for=""><span class="bFeildStarImg">*</span><?=$form->name->title?></label>
			<div class="bBuyingLine__eRight">
				<?php if(isset($form->name->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->name->error?></div></div>
				<?php endif; ?>
				<input type="text" value="<?=$form->name->value?>" name="title" class="bBuyingLine__eText mInputLong">
			</div>
			<?php endif; ?>
			
			<?php if(isset($form->orderIds)):?>
			<label class="bBuyingLine__eLeft" for=""><span class="bFeildStarImg">*</span><?=$form->orderIds->title?></label>
			<div class="bBuyingLine__eRight">
				<?php if(isset($form->orderIds->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->orderIds->error?></div></div>
				<?php endif; ?>
				<input type="text" value="<?=$form->orderIds->value?>" name="orderIds" class="bBuyingLine__eText mInputLong">
			</div>
			<?php endif; ?>

			<?php if(isset($form->email)):?>
			<label class="bBuyingLine__eLeft" for=""><span class="bFeildStarImg">*</span><?=$form->email->title?></label>
			<div class="bBuyingLine__eRight">
				<?php if(isset($form->email->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->email->error?></div></div>
				<?php endif; ?>
				<input type="text" value="<?=$form->email->value?>" name="email" class="bBuyingLine__eText mInputLong mInput265">
			</div>
			<?php endif; ?>

			<?php if(isset($form->mobile)):?>
			<label class="bBuyingLine__eLeft" for="mobile"><span class="bFeildStarImg">*</span><?=$form->mobile->title?></label>
			<div class="bBuyingLine__eRight mPhone">
				<span class="bPlaceholder">+7</span> 
				<?php if(isset($form->mobile->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->mobile->error?></div></div>
				<?php endif; ?>
				<input type="text" value="<?=$form->mobile->value?>" name="mobile" class="bBuyingLine__eText mInputLong" id="mobile">
			</div>
			<script type="text/javascript">jQuery(function($){$("#mobile").mask("(999) 999 99 99");})</script>
			<?php endif; ?>
			
			<label class="bBuyingLine__eLeft" for="file"><span class="bFeildStarImg">*</span><?=$form->file->title?></label>
			<div class="bBuyingLine__eRight">
				<label class="pc_file" for="file">Выбрерите файл
					<input type="file" name="file" id="file">
					<?php if(isset($form->file->error)):?><div class="bErrorText"><?=$form->file->error?></div><?php endif?>
				</label>
				<br/><small>Максимальный размер файла <?=str_replace('M','Мб',ini_get('upload_max_filesize'))?><br/>Допустимые форматы jpeg, gif, png</small>
			</div>
			
			<?php if(isset($form->isAccept)):?>
			<label class="bBuyingLine__eLeft" for=""></label>
			<div class="bBuyingLine__eRight">
				<?php if(isset($form->isAccept->error)): ?>
				<div class="bErrorText"><div class="bErrorText__eInner"><?=$form->isAccept->error?></div></div>
				<?php endif; ?>
				<div class="bSubscibeCheck bInputList" style="width: 400px">
					<input type="checkbox" <?=($form->isAccept->value?'checked="checked"':null)?>class="jsCustomRadio bCustomInput mCustomCheckBig" id="isAccept" name="isAccept" value="1">
					<label for="isAccept" class="bCustomLabel mCustomLabelBig mChecked" style="height: 30px">
						Принимаю <a href="/transformers" target="_blank">условия участия в фотоконкурсе</a>
					</label>
				</div>
			</div>
			<?php endif; ?>
			
			<div class="bBuyingLine mConfirm clearfix">
				<div class="bBuyingLine__eLeft"></div>

				<input type="submit" value="Подтвердить"/>
			</div>
		</div>
	</form>
	<?php endif; ?>
</div>