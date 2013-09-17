/**
 * Обработчик страницы со стоимостью услуг
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){

  var buildTable = function( table, rows ) {
    for (var i = 0; i < rows.length; i++) {
      table.find('tbody').append(i + ' ' + rows[i]['Стоимость'] + ' ' + rows[i]['Услуга']);
    };
  }

  $(document).ready(function() {
    if ( $('#region_list').length ) {
      data = $('#region_list').data('service');
      table = $('.bServicesTable');

      if ( data ) {
        buildTable( table, data[$('#region_list').val()] );
      }
    }
  });
}());


