$(function() {
	var data = $('#contentPageData').data('data');

	if (!ENTER.utils.objLen(data.services)) {
		return;
	}

	var
		$regions = $('.js-content-services-regions'),
		$tableBody = $('.js-content-services-tableBody')
	;

	function createTable( chosenRegionName ) {
		var tableData = data.services[chosenRegionName],
			i,
			key,
			tmpTr;

		$tableBody.empty();

		if ( tableData instanceof Array ) {
			// просто выводим элементы

			for ( i = 0; i < tableData.length; i++ ) {
				tmpTr = '<tr>'+
							//'<td>'+ (i + 1) +'</td>'+
							'<td>'+ tableData[i]['Услуга'] +'</td>'+
							'<td>'+ tableData[i]['Стоимость'] +'</td>'+
						'</tr>';

				$tableBody.append(tmpTr);
			}
		}
		else if ( tableData instanceof Object ) {
			// элементы разбиты на категории

			for ( key in tableData ) {
				if ( tableData.hasOwnProperty(key) ) {
					tmpTr = '<tr>'+
								//'<th></th>'+
								'<th><strong>'+ key +'</strong></th>'+
								'<th></th>'+
							'</tr>';

					$tableBody.append(tmpTr);

					for ( i = 0; i < tableData[key].length; i++ ) {
						tmpTr = '<tr>'+
									//'<td>'+ (i + 1) +'</td>'+
									'<td>'+ tableData[key][i]['Услуга'] +'</td>'+
									'<td>'+ tableData[key][i]['Стоимость'] +'</td>'+
								'</tr>';

						$tableBody.append(tmpTr);
					}
				}
			}
		}
	}

	$regions.on('change', function() {
		createTable($(this).val());
	});

	if (data.regionName && $regions.length) {
		var hasRegion = false;
		$regions.find('option').each(function() {
			var $option = $(this);
			if ($option.text() && -1 !== $option.text().indexOf(data.regionName)) {
				hasRegion = true;
				$option.prop('selected', true).change();
			}
		});

		if (!hasRegion) {
			$regions.find('option:first').prop('selected', true).change();
		}
	}
});