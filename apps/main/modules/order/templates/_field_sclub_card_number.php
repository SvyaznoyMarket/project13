</div>
<div class="fl width500 pb25"></div>
<div class="fl width215 mr20"><strong class="font16">У вас есть карта "Связной-Клуб"?</strong></div>
<div class="fl width430">
	<div class="pb10">
		<?php echo $form['sclub_card_number']->renderLabel() ?>
		<?php echo $form['sclub_card_number']->renderError() ?>
	</div>

		<?php echo $form['sclub_card_number']->render(array('class' => 'text width418 mb15', )) ?>
	<div class="font11">Введите без пробелов 16 цифр с лицевой стороны карты Связной-Клуб</div>