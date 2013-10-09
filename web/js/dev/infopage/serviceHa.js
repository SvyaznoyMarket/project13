/**
 * Обработчик страницы со стоимостью услуг
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){

  var appendGroupHeader = function( table, group ) {
    var groupData = {
      group: group
    };

    table.find('tbody').append( tmpl('groupHeaderTemplate', groupData) );
  };

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

  var buildGroup = function( table, group, rows ) {
    if ( group ) {
      appendGroupHeader( table, group );
    }

    if ( rows ) {
      appendRows( table, rows );
    }
  }

  var buildTable = function( table, data ) {
    table.find('tbody').html('');
    var groups = data[ $('#region_list').val() ];

    for(group in groups) {
      buildGroup( table, group, groups[group] )
    }
  }

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


