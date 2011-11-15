<!-- Search result  -->
<div class="popup popupbox" id="search_popup-block">
  <i title="Закрыть" class="close">Закрыть</i>
  <h2 class="pouptitle">Результаты поиска</h2>

  <div class="searchtitle">
    Вы искали <span class="orange">"<?php echo $searchString ?>"</span> товары не найдены.
  </div>

  <p>Попробуйте найти заново:</p>

  <?php include_component('search', 'form', array('searchString' => $searchString, 'wide' => true)) ?>

</div>
<!-- /Search result -->