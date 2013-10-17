/**
 * Обработчик страницы со стоимостью услуг
 *
 * @requires  jQuery
 */
;(function(global) {	
	var serviceData = $('#contentPageData').data('data'),
		selectRegion = $('#region_list'),
		serviceTableContent = $('#bServicesTable tbody');
	// end of vars


	var createTable = function createTable( chosenRegion ) {
			var tableData = serviceData[chosenRegion],
				i,
				key,
				tmpTr;
			// end of vars

			serviceTableContent.empty();

			if ( tableData instanceof Array ) {
				// просто выводим элементы

				for ( i = 0; i < tableData.length; i++ ) {
					tmpTr = '<tr>'+
								'<td>'+ (i + 1) +'</td>'+
								'<td>'+ tableData[i]['Услуга'] +'</td>'+
								'<td>'+ tableData[i]['Стоимость'] +'</td>'+
							'</tr>';

					serviceTableContent.append(tmpTr);
				}
			}
			else if ( tableData instanceof Object ) {
				// элементы разбиты на категории

				for ( key in tableData ) {
					if ( tableData.hasOwnProperty(key) ) {
						tmpTr = '<tr>'+
									'<th></th>'+
									'<th><strong>'+ key +'</strong></th>'+
									'<th></th>'+
								'</tr>';

						serviceTableContent.append(tmpTr);

						for ( i = 0; i < tableData[key].length; i++ ) {
							tmpTr = '<tr>'+
										'<td>'+ (i + 1) +'</td>'+
										'<td>'+ tableData[key][i]['Услуга'] +'</td>'+
										'<td>'+ tableData[key][i]['Стоимость'] +'</td>'+
									'</tr>';

							serviceTableContent.append(tmpTr);
						}
					}
				}
			}
		},

		/**
		 * Обработка полченных данных
		 */
		prepareData = function prepareData( data ) {
			var i,
				key,
				tmpOpt,
				initVal;
			// end of vars
			
			console.info('prepareData');

			selectRegion.empty();

			for ( key in data ) {
				if ( data.hasOwnProperty(key) ) {
					tmpOpt = $('<option>').val(key).html(key);
					selectRegion.prepend(tmpOpt);
				}
			}

			initVal = selectRegion.find('option:first').val();

			selectRegion.val(initVal);
			createTable(initVal);
		},

		/**
		 * Хандлер смены региона
		 */
		changeRegion = function changeRegion() {
			var self = $(this),
				selectedRegion = self.val();
			// end of vars

			createTable(selectedRegion);
		};
	// end of function

	if ( serviceData ) {
		prepareData(serviceData);
		selectRegion.on('change', changeRegion);
	}

}(this));