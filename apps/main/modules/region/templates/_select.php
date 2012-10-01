<!--<div class="popup" id="region-block" style="width: 640px;">
  <i title="Закрыть" class="close">Закрыть</i>
  <div class="popupbox width694 height250">
    <h2 class="pouptitle">Привет! Укажите, из какого вы города.</h2>

    <form class="ui-css">
      <input id="jscity" data-url-autocomplete="<?php echo url_for('region_autocomplete') ?>" value="" class="bBuyingLine__eText mInputLong" />
      <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton mDisabled" />
      <div id="jscities" style="position:relative"></div>
    </form>

    <div class="pt10">
      <?php include_component('region', 'top_list') ?>
    </div>

  </div>
</div>-->
<div class="popupRegion" style="display:none">
	<a href="#" class="close">Закрыть</a>
	<h2 class="pouptitle">Привет! Укажите, из какого вы города.</h2>
	<div class="hidden">
		<p>скрытый блок</p>
	</div>
	<form class="ui-css">
		<input id="jscity" 
			data-url-autocomplete="<?php echo url_for('region_autocomplete') ?>" placeholder="Введите свой город" class="bBuyingLine__eText mInputLong" /><a class="inputClear" href="#">x</a>
		<input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton mDisabled" />
		<div id="jscities" style="position:relative"></div>
    </form>
    <div class="cityInline">
    	<a href="#">Москва</a>
    	<a href="#">Санкт-Петербург</a>
    </div>
	<div class="colomn">
		<a href="#">Москва</a>
		<a href="#">Воронеж</a>
		<a href="#">Саратов</a>
		<a href="#">Липецк</a>
		<a href="#">Хабаровск</a>
		<a href="#">Владивосток</a>
	</div>
	<div class="colomn">
		<a href="#">Москва</a>
		<a href="#">Воронеж</a>
		<a href="#">Саратов</a>
		<a href="#">Липецк</a>
		<a href="#">Хабаровск</a>
		<a href="#">Владивосток</a>
	</div>
	<div class="colomn">
		<a href="#">Москва</a>
		<a href="#">Воронеж</a>
		<a href="#">Саратов</a>
		<a href="#">Липецк</a>
		<a href="#">Хабаровск</a>
		<a href="#">Владивосток</a>
	</div>
	<div class="clear"></div>
	<div class="info">
		<p>Мы доставим вашу покупку в любой регион. 
			Если в вашем регионе еще нет нашего офиса, доставка будет осуществляться с помощью партнеров.</p>
	</div>
</div>