<div class="popupRegion" style="display:none">
  <a href="#" class="close">Закрыть</a>
  <h2 class="pouptitle">Привет! Укажите, из какого вы города.</h2>
  <div class="hidden">
    <p>скрытый блок</p>
  </div>
  <form class="ui-css">
    <input id="jscity"
           data-url-autocomplete="<?php echo $this->url('region.autocomplete') ?>" placeholder="Введите свой город" class="bBuyingLine__eText mInputLong" /><a class="inputClear" href="#">x</a>
    <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton mDisabled" />
    <div id="jscities" style="position:relative"></div>
  </form>
  <div class="cityInline">
    <a href="<?php echo $this->url('region.change', array('region' => 14974)) ?>">Москва</a>
    <!--a href="#">Город автоопределения</a -->
  </div>
  <?php $offset = 0; ?>
  <?php foreach ($columns_count as $count): ?>
  <div class="colomn">
    <?php for ($i = 0; $i < $count; $i++): ?>
    <?php $region = $regionTopList[$offset + $i]; ?>
    <a href="<?php echo $this->url('region.change', array('region' => $region->getId())) ?>"><?php echo $region->getName() ?></a>
    <?php endfor ?>
    <?php $offset += $count; ?>
  </div>
  <?php endforeach; ?>
  <div class="clear"></div>
  <!--div class="info">
    <p>Мы доставим вашу покупку в любой регион.
      Если в вашем регионе еще нет нашего офиса, доставка будет осуществляться с помощью партнеров.</p>
  </div-->
</div>