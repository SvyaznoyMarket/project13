/**
 * Обработчик страницы со стоимостью услуг
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){

  var buildTable = function( table, data ) {
    table.find('tbody').html('');
    var rows = data[$('#region_list').val()];
    if ( rows ) {
      appendRows( table, rows );
    }
  }

  var appendRows = function( table, rows ) {
    for (var i = 0; i < rows.length; i++) {
      var rowData = {
        counter: i + 1,
        service: rows[i]['Услуга'],
        price: rows[i]['Стоимость']
      };

      table.find('tbody').append( tmpl('rowTemplate', rowData) );
    }
  };

  $(document).ready(function() {
    if ( $('#region_list').length ) {
      var data = $('#contentPageData').data('data');

      $('#regionListPlaceholder').replaceWith( $('#region_list') );
      $('#bServicesTablePlaceholder').replaceWith( $('.bServicesTable') );

      var table = $('.bServicesTable');

      if ( data ) {
        buildTable( table, data );
        $('#region_list').on('change', function(){
          buildTable( table, data );
        });
      }
    }
  });
}());


