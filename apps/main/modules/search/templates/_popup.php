<!-- Search result  -->
<div class="popup popupbox" id="search_popup-block">
  <i title="Закрыть" class="close">Закрыть</i>
  <h2 class="pouptitle">Результаты поиска</h2>

  <?php include_partial('search/product_count', $sf_data) ?>

  <h2>Попробуйте найти заново:</h2>

  <?php include_component('search', 'form', array('searchString' => $searchString, 'wide' => true)) ?>

</div>
<!-- /Search result -->