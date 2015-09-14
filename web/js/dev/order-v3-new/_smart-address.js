;(function(w,ko,$) {

	var address,
		initialAddressData = $('#jsUserAddress').data('value'),
		kladrConfig = $('#kladr-config').data('value'),
        region = $('#page-config').data('value').user.region;

	function AddressModel () {

		var self = this,
			streetTypeDefault = 'Улица';

		// сокращенные названия улиц из КЛАДРА (для автосогласования в 1C)
		self.streetShortToLong = {
			'пр-кт': 'Проспект',
			'ш': 'Шоссе',
			'ул.': 'Улица',
			'ул': 'Улица',
			'пер': 'Переулок',
			'пл': 'Площадь',
			'дор': 'Дорога'
		};

		self.cityName = ko.observable('');
		self.cityId = ko.observable(0);
		self.streetName = ko.observable('');
		self.streetType = ko.observable(streetTypeDefault);
		self.streetTypeShort = ko.observable('');
		self.streetId = ko.observable(0);
		self.buildingName = ko.observable('');
		self.buildingId = ko.observable(0);
		self.apartmentName = ko.observable('');

		self.inputFocus = ko.observable(true);
		self.inputPrefix = ko.computed(function(){
			if (self.streetName() == '') return 'Улица:';
			else if (self.buildingName() == '') return 'дом:';
			else return 'квартира:';
		});

		// return {type, parentType, parentId} or false
		self.getParent = function(){
			var result = false;
			if (self.cityId() != 0 && self.inputPrefix() == 'Улица:') result = { type: $.kladr.type.street, parentType: 'city', parentId: self.cityId() };
			else if (self.streetId() != 0  && self.inputPrefix() == 'дом:') result = { type: $.kladr.type.building ,parentType: 'street', parentId: self.streetId() };
			return result;
		};

		self.update = function(val) {
			// обработка ручного ввода
			if (typeof val == 'string') {
				if (self.streetName() == '') {
					self.streetName(val);
					self.streetTypeShort('');
				}
				else if (self.buildingName() == '') self.buildingName(val);
				else if (self.apartmentName() == '') self.apartmentName(val);
			}
			// обработка автодополнения
			else if (typeof val == 'object') {
				if (val.contentType == 'street') {
					self.streetName(val.name).streetId(val.id).streetType(val.type).streetTypeShort(val.typeShort)
				}
				else if (val.contentType == 'building') {
					self.buildingName(val.name).buildingId(val.id)
				}
			}
		};

		self.clearCity = function() { self.cityName('').cityId(0); return self };
		self.clearStreet = function() { self.streetName('').streetType(streetTypeDefault).streetId(0); return self};
		self.clearBuilding = function() { self.buildingName('').buildingId(0); return self };
		self.clearApartment = function() { self.apartmentName(''); return self };

		return self;

	}

	function saveAddress(address) {

		if (address.streetName() == '') return;

		$.ajax({
			type: 'POST',
			data: {
				'action' : 'changeAddress',
				'params' : {
					// сохраняем улицу в формате "Название + сокращенный тип" для автосогласования в 1С
					street: address.streetName() + ' ' + (address.streetTypeShort() == '' ? address.streetType() : address.streetTypeShort()),
					building: address.buildingName(),
					apartment: address.apartmentName(),
					kladr_id: address.buildingId() != 0 ? address.buildingId() : address.streetId() != 0 ? address.streetId() : address.cityId() != 0 ? address.cityId() : '' }
			}
		}).fail(function(jqXHR){
			var response = $.parseJSON(jqXHR.responseText);
			if (response.result) {
				console.error(response.result);
			}
		}).done(function(data){
//			console.log("Query: %s", data.result.OrderDeliveryRequest);
			console.log("Saved address:", data.result.OrderDeliveryModel.user_info.address);
		})
	}

	function autoCompleteRequest (request, response) {
		if (address.getParent() !== false) {
			var query = $.extend({}, { limit: 10, name: request.term }, address.getParent());
//			if (spinner) spinner.spin($('.kladr_spinner')[0]);
			console.log('[КЛАДР] запрос: ', query);
			$.kladr.api(query, function (data) {
				console.log('[КЛАДР] ответ', data);
//				if (spinner) spinner.stop();
				response($.map(data, function (elem) {
					return { label: formatStreetName(elem) , value: elem }
				}))
			});
		}
	}

	function formatStreetName(elem) {
		var name = elem.name,
			typeShort = elem.typeShort,
			dot = '';
		if ($.inArray(typeShort, ['ул', 'пер', 'пл', 'дор']) != -1) dot = '.';
		if (elem.contentType === 'street') {
			return name + ' ' +  typeShort + dot;
		} else {
			return name;
		}
	}

	// чтобы вызывать функцию после AJAX-запросов, копируем её в глобальную переменную
	ENTER.OrderV3.constructors.smartAddressInit = function() {
		var $input = $('.jsSmartAddressInput'),
			initKladrQuery = $.extend(kladrConfig, {'limit': 1, type: $.kladr.type.city, name: region.name});

		// jQuery-ui autocomplete from КЛАДР
		$input.autocomplete({
//            appendTo: '#kladrAutocomplete',
			source: autoCompleteRequest,
			minLength: 1,
			open: function( event, ui ) {
				$('.ui-autocomplete').css({'position' : 'absolute', 'top' : 29, 'left' : 0});
			},
			select: function( event, ui ) {
				this.value = '';
				$input.val('');
				address.update(ui.item.value);
				return false;
			},
			focus: function( event, ui ) {
				this.value = ui.item.label;
				event.preventDefault(); // without this: keyboard movements reset the input to ''
				event.stopPropagation(); // without this: keyboard movements reset the input to ''
			},
			change: function( event, ui ) {
			},
			messages: {
				noResults: '',
				results: function() {}
			}
		});

		// Обработка event-ов на поле ввода
		$input.on({
			keypress: function(e){
				// Нажатие ENTER означает ручной ввод улицы, дома, квартиры
				if (e.which == 13) {
					if ($(this).val().length > 0) {
						address.update($(this).val()); // обновляем
						$input.val(''); // очищаем поля ввода
						$input.autocomplete('close'); // скрываем автокомплит
					}
				}
			},
			keydown: function(e){
				// Обработка Backspace
				var key = e.keyCode || e.charCode;
				if (key === 8 && $(this).val().length === 0) {
					if (address.inputPrefix() == 'дом:') {
						$input.val(address.streetName());
						address.clearBuilding().clearStreet();
					}
					if (address.inputPrefix() == 'квартира:') {
						$input.val(address.buildingName());
						address.clearApartment().clearBuilding();
					}
					e.preventDefault();
				}
			},
			blur: function(){
				address.update($(this).val()); // обновляем
				$input.val(''); // очищаем поле ввода
				saveAddress(address);
			}
		});

		// клик по блоку (улица, дом, квартира) в адресе
		$('.jsSmartAddressEditField').on('click', function(){
			var dataType = $(this).data('type');
			if (dataType && typeof address[dataType] == 'function') {
				$input.val(address[dataType]()); // записываем значение в поле ввода
				if (dataType == 'apartmentName') address.clearApartment();
				if (dataType == 'buildingName') address.clearBuilding().clearApartment();
				if (dataType == 'streetName') {
					address.clearStreet().clearBuilding().clearApartment();
					if (address.streetTypeShort() != '') $input.val($input.val() + ' ' + address.streetTypeShort()); // дописываем сокращенное название
				}
			}
		});

		if (region.kladrId) {
            address.cityId(region.kladrId)
        } else if (kladrConfig && region.name) {
			$.kladr.api(initKladrQuery, function (data){
				var id = data.length > 0 ? data[0].id : 0;
				if (id==0) console.error('КЛАДР не определил город, конфигурация запроса: ', initKladrQuery);
				else address.cityId(data[0].id);
			})
		}

	};

	// начинаем отсюдова

	address = new AddressModel();

	// Заполняем модель данными при загрузке или рефреше страницы
	if (typeof initialAddressData == 'object') {
		if (initialAddressData.street) {
			var regexResult = initialAddressData.street.match(/(.+)\s+(.+)$/);
			if (regexResult) {
				if (address.streetShortToLong.hasOwnProperty(regexResult[2])) {
					address.streetType(address.streetShortToLong[regexResult[2]]);
					address.streetTypeShort(regexResult[2]);
				} else {
					address.streetType(regexResult[2]);
				}
				address.streetName(regexResult[1]);
			}
		}
		if (initialAddressData.building) address.buildingName(initialAddressData.building);
		if (initialAddressData.apartment) address.apartmentName(initialAddressData.apartment);
	}

    $.each($('.jsAddressRootNode'), function(i,val){
        ko.applyBindings(address, val);
    });

	ENTER.OrderV3.address = address;
	ENTER.OrderV3.constructors.smartAddressInit();

}(window, ko, jQuery));