$(document).ready(function() {

	/* View Models */
	
	function OneCViewModel() {

			var self = this;
			self.noDelivery = ko.observable(false);
			
			self.title     = Model.jstitle;
			self.price     = Model.jsprice;
			self.icon      = Model.jsbimg;
			self.shortcut  = Model.jsshortcut;
			self.stockq    = Model.jsstock;
			
			self.quantity    = ko.observable(1);
			self.quantityTxt = ko.computed(function() {
				return self.quantity() + ' шт.';
			}, this);
			self.priceTxt = ko.computed(function() {
				return printPrice( self.price );
			}, this);

			self.newWarehouse = Model.is_quick_only;

			self.formStatus = ko.observable( 'typing' ); // 'process' 'error' 'sending'
			self.formStatusTxt = ko.computed( function() {
				var status = '';
				switch( self.formStatus() ) {
					case 'reserve':
						status = 'Зарезервировать';
						break;
					case 'typing':
						status = 'Отправить заказ';
						break;
					case 'process':
						status = 'Проверка...';
						break;
					case 'error':
						status = 'Отправить заказ нельзя';
						break;
					case 'sending':
						status = 'Отправка...';
						break;
				}
				return status;
			}, this);

			var scNum = window.docCookies.getItem("scId"); //берем номер карты «связного клуба» из куки

			self.showMap = ko.observable( false );
			self.textfields = [];

			var firstNameVal = ( $('#oneClick').length ) ? $('#oneClick').data('values').recipient_first_name : '',
				phoneNumberVal = ( $('#oneClick').length ) ? $('#oneClick').data('values').recipient_phonenumbers : '',
				emailVal = ( $('#oneClick').length ) ? $('#oneClick').data('values').recipient_email : '';
			//end of vars

			self.textfields.push( ko.observable({
				title: 'Имя получателя',
				name: 'order[recipient_first_name]', //UNIQUE!
				selectorid: '',
				value: firstNameVal,
				valerror: false,
				showsubscribe: false,
				regexp: /^[ёa-zа-я\s]+$/i
			}) );
			self.textfields.push( ko.observable({
				title: 'Телефон для связи',
				name: 'order[recipient_phonenumbers]', //UNIQUE!
				selectorid: 'phonemask',
				value: phoneNumberVal,
				valerror: false,
				showsubscribe: false,
				regexp: /^[()0-9\-\+\s]+$/
			}) );
			self.textfields.push( ko.observable({
				title: 'E-mail (если есть)',
				name: 'order[recipient_email]', //UNIQUE!
				selectorid: 'recipientEmail',
				value: emailVal,
				valerror: false,
				showsubscribe: true,
				active: true,
				regexp: /./
			}) );
			self.textfields.push( ko.observable({
				title: 'Номер вашей карты «Связной-Клуб»',
				name: 'order[recipient_scCard]', //UNIQUE!
				selectorid: 'scCard',
				value: scNum,
				valerror: false,
				showsubscribe: false,
				regexp: /^[()0-9\-\s]+$/
			}) );

			self.disabledSelectors = ko.observable( false );
			self.noQBar            = ko.observable( false );
			self.stableType        = ko.observable( false );
			
			// for dynModel
			self.chosenDlvr = ko.observable( {} );
			self.chosenDate = ko.observable( {} );
			self.dlvrs = ko.observableArray([]);
			self.dates = ko.observableArray([]);
			self.shops = ko.observableArray([]);
			self.chosenShop = ko.observable( {} );
			self.pickedShop = ko.observable( {} );

			self.dynModel = function( Deliveries ) {
				var chosenType = self.chosenDlvr().type;

				if ( !chosenType ){
					chosenType = 'self';
				}

				self.dlvrs.removeAll();
				// self.chosenDlvr({});

				for ( var obj in Deliveries ) {
					self.dlvrs.push( {
						type: obj,
						name: Deliveries[obj].name,
						modeID: Deliveries[obj].modeId,
						price: Deliveries[obj].price
					});

					if( obj == chosenType ){
						self.chosenDlvr( self.dlvrs()[ self.dlvrs().length - 1 ] );
					}
				}

				if ( !('type' in self.chosenDlvr() ) ){
					self.chosenDlvr( self.dlvrs()[ 0 ] );
				}
				
				console.log(self.chosenDlvr());

				self.dates( Deliveries[ self.chosenDlvr().type+'' ].dates.slice(0) );
				self.chosenDate( self.dates()[0] );
				
				console.log(selfAvailable);

				if ( selfAvailable ) {
					if ( Deliveries.hasOwnProperty('self') ) {
						console.log('self shops');
						self.shops( Deliveries['self'].shops.slice(0) );
					}
					else {
						console.log('now shops');
						self.shops( Deliveries['now'].shops.slice(0) );
					}

					self.chosenShop( self.shops()[0] );
					self.pickedShop( self.shops()[0] );

					// self.showMap(true)
				}
				else {
					var leer = { address: '', regtime: '', id : 1 };
					self.chosenShop( leer );
					self.pickedShop( leer );
				}
			};

			self.dynModel( Deliveries );

			self.total = ko.computed(function() {
				return printPrice( self.price * self.quantity() + self.chosenDlvr().price * 1 );
			}, this);
			
			self.changeDlvr = function() {
				console.info('changeDlvr');
				var typeDlvr = self.chosenDlvr().type;
				self.dates.removeAll();
				while( self.dates().length ) {
					self.dates.pop();
				}

				for(var i=0; i< Deliveries[ typeDlvr ].dates.length; i++ ){
					self.dates().push(Deliveries[ typeDlvr ].dates[i]);
				}

				self.chosenDate( self.dates()[0] );
				
				if ( self.showMap() ) {
					self.showMap( false );
				}
				if ( typeof(window.regionMap)=='undefined' ) {
					console.warn('region map undefined');
					$('.bFast__eMapLink').remove();
				}
			};
			
			self.plusItem = function() {
				self.quantity( self.quantity() + 1 );

				if( self.quantity() > self.stockq ) {
					self.noDelivery( true );
					return false;
				}
				
				var curq =  self.quantity() * 1;
				setTimeout(function(){
					self.loadData( curq, 1 );
				}, 500 );

				kissAnalitycs();

				return false;
			};

			self.minusItem = function() {
				if( self.quantity() == 1 ){
					return false;
				}
				self.quantity( self.quantity() - 1 );

				if( self.noDelivery() ){
					if( self.quantity() <= self.stockq ) {
						self.noDelivery( false );
					}
					else {
						return false;
					}
				}

				var curq =  self.quantity() * 1;
				setTimeout(function(){
					self.loadData( curq, -1 );
				}, 500 );

				kissAnalitycs();

				return false;
			};

			var kissAnalitycs = function kissAnalitycs() {
				var toKISS_set = {
					'Checkout Step 1 SKU Quantity':self.quantity() * 1,
					'Checkout Step 1 SKU Total':self.price * self.quantity() * 1,
					'Checkout Step 1 Order Total':self.price * self.quantity() * 1 + self.chosenDlvr().price * 1
				};

				if ( typeof(_kmq) !== 'undefined' ) {
					_kmq.push(['set',toKISS_set]);
				}
			};
			
			self.preparedData = function( data ) {
				if ( data.type === 'self' || data.type === 'now' ) {
					self.formStatus('reserve');

					for ( var i = 0, l = self.dlvrs().length; i < l; i++ ) {
						if ( self.dlvrs()[i].type === 'self' || self.dlvrs()[i].type === 'now') {
							self.chosenDlvr( self.dlvrs()[i] );
							break;
						}
					}

					self.disabledSelectors( true );
					self.noQBar( true );
					self.chooseShopById(data.shop.id);
					if( 'date' in data ){
						self.chosenDate( data.date );
					}
					if( 'shop' in data ){
						self.chooseShopById(data.shop.id);
					}
				}
				else if( data.type === 'courier' ) {
					self.formStatus('reserve');
					for(var j = 0, len = self.dlvrs().length; j < len; j++ ){
						if ( self.dlvrs()[j].type !== 'self' &&  self.dlvrs()[j].type !== 'now' ) {
							self.chosenDlvr( self.dlvrs()[j]  );
							break;
						}
					}

					self.disabledSelectors( false );
					self.stableType( true );
					self.noQBar( true );
				}
			};
			
			self.loadData = function( momentq, direction ) {
				console.info('loadData')
				if( ( direction > 0 && self.quantity() > momentq ) || ( direction < 0 && self.quantity() < momentq ) ){
					return;
				}
				var postData = {
					product_id: Model.jsitemid,
					product_quantity: momentq,
					region_id: Model.jsregionid*1
				};
				
				$.post( inputUrl, postData, function(data) {
					if( self.noDelivery() ){
						return false;
					}
					if( !data.success ) {
						self.noDelivery(true);
						return false;
					}

					Deliveries = data.data;
					var le = 0;
					for(var key in Deliveries ){
						le++;
					}
					if( le === 0 ) {
						self.noDelivery(true);
						return false;
					}
					else {
						self.noDelivery(false);
					}
					selfAvailable = ('self' in Deliveries) || ('now' in Deliveries);

					console.log(selfAvailable);

					self.dynModel(Deliveries);
					if ( selfAvailable && typeof(mapCenter) == 'undefined') {
						mapCenter = calcMCenter( Deliveries['self'].shops );
					}						
					if ( selfAvailable && ! ('regionMap' in window ) ) {
						var mapCallback = function() {
							window.regionMap.addHandler( '.shopchoose', pickStoreMVMCL );
						};

						MapInterface.init( mapCenter, 'mapPopup', mapCallback );
					}
					
				});
			};
			
			self.pickDate = function( ) {
				//shops mod	
				if( selfAvailable ) {
					if( 'shopIds' in item ) {
						if( item.shopIds.length > 0 ) {
							self.shops.removeAll();// = ko.observableArray( Deliveries['self'].shops.slice(0) )
							for(var key in Deliveries['self'].shops ) {
								if( $.inArray(Deliveries['self'].shops[key].id, item.shopIds) !== -1 ){
									self.shops.push( Deliveries['self'].shops[key] );
								}
							}
						}
					}
				}
				if( self.showMap() ){
					self.showMarkers();
				}
			};
			
			self.pickShop = function( shid ) {
				for(var i=0, l=self.shops.length; i<l; i++){
					if( self.shops[i].id == shid ) {
						self.chosenShop( self.shops[i] );
						return;
					}
				}
			};

			self.pickShopOnMap = function( shid ) {
				for(var i=0, l=self.shops().length; i<l; i++){
					if( self.shops()[i].id == shid ) {
						self.pickedShop( self.shops()[i] );
						return;
					}
				}
			};
			
			self.toggleMap = function() {
				if( self.showMap() ) { // like toggle but more precise
					$('#mapPopup').slideUp(500, function() {
						self.showMap(false);
					});
				}
				else {
					$('#mapPopup').slideDown(500, function(){
						self.showMap(true);
						self.showMarkers();
					});
				}				
			};
			
			self.turnOffMap = function() {
				console.log('tick');
				self.showMap(false);
			};
			
			self.showMarkers = function() {
				var markersPull = {},
					tmp = self.shops(); //MVM.popupWithShops()
				//end of vars
				
				for(var i = 0, l = tmp.length; i < l; i++) {
					var key = tmp[i].id * 1;
					markersPull[ key ] = {
						id: tmp[i].id,
						name: tmp[i].address,
						regtime: tmp[i].regtime,
						latitude: tmp[i].latitude,
						longitude: tmp[i].longitude
					};
				}

				window.regionMap.showMarkers( markersPull );
			};
			
			self.chooseShopById = function( shopnum ) {
				for(var i = 0, l = self.shops().length; i < l; i++) {
					if( self.shops()[i].id == shopnum ) {
						self.chosenShop( self.shops()[i] );
						break;
					}
				}
			};

			self.validateField = function( textfield, e ) {
				var valerror = false;

				if ((e.currentTarget.name == 'order[recipient_scCard]' || e.currentTarget.name === 'order[recipient_email]')&&(e.currentTarget.value == '')){
					return true;
				}

				if ( e.currentTarget.name === 'order[recipient_email]' && !e.currentTarget.value.isEmail() ) {
					valerror = true;
					self.formStatus('typing');
				}

				if( e.currentTarget.name !== 'order[recipient_email]' && ( e.currentTarget.value.replace(/\s/g, '') == '' || !textfield.regexp.test( e.currentTarget.value ) ) ) {
					valerror = true;
					self.formStatus('typing');
				}

				if( e.currentTarget.getAttribute('id') === 'phonemask' && e.currentTarget.value.replace(/[^0-9]/g, '').length !== 10 ) {
					valerror = true;
					self.formStatus('typing');
				}

				for ( var i = 0, l = self.textfields.length; i < l; i++ ) { // like indexOf
					if ( self.textfields[i]().name === textfield.name ) {
						var tmp = self.textfields[i]();
						tmp.valerror = valerror;
						tmp.value = e.currentTarget.value;

						if ( e.currentTarget.name === 'order[recipient_email]' ) {
							tmp.active = !!parseInt( $('#order1click-container-new .bSubscibe input[name="subscribe"]').val() );
						}

						self.textfields[i]( tmp );

						break;
					}
				}

				enableHandlers();
				return true;
			};

			self.validateForm = function() {
				if ( self.noDelivery() ){
					return false;
				}

				if ( self.formStatus() !== 'typing' && self.formStatus() !== 'reserve' ) { // double or repeated click
					return;
				}
				//change title
				self.formStatus('process');
				
				//validate fields
				$('#oneClick input').trigger('change');

				if( self.formStatus() === 'typing' ){ // validation error
					return;
				}

				//send ajax
				self.sendData();
				
			};
						
			self.sendData = function() {
				self.formStatus('sending');
				$('.bFastInner tbody tr:last').empty();

				var postData = {
					'order[product_quantity]' : self.quantity(),
					'order[delivered_at]' : self.chosenDate().value,
					'order[delivery_type_id]': ( self.chosenDate().isNow ) ? 4 : self.chosenDlvr().modeID
				};

				if ( self.chosenDlvr().type == 'self' || self.chosenDlvr().type == 'now' ) {
					postData[ 'order[shop_id]' ] = self.chosenShop().id;
				}

				for(var i=0,l=self.textfields.length; i<l; i++){
					postData[ self.textfields[i]().name + '' ] = self.textfields[i]().value;
				}
				postData['subscribe'] = $('#order1click-container-new .bSubscibe input[name="subscribe"]').val();

				if ( typeof(window.KM) !== 'undefined' ) {
					postData['kiss_session'] = window.KM.i;
				}

				$.ajax( {
					type: 'POST',
                    timeout: 60000,
					url: outputUrl,
					data: postData,
					success: function( data, textStatus ) {
						var bFast = $('.bFast');

						if( !data.success || textStatus !== 'success' ) {
							self.formStatus('typing');
							$('.bFastInner tbody tr:last').append('<td colspan="2" class="red">'+data.message+'</td>');
							return;
						}

						// Запускаем скрипты Аналитики для завершенного заказа в 1клик
						OC_MVM.AnalyticsComplete(data.data);

						// console.log(data)
						//process
						//$('.bFast').parent().append( data.data.content );
						try{
							bFast.parent().append( data.data.content );
						}catch(e){
							console.log('### jQ append error:');
							console.log(e);
						}
						bFast.remove();
						$('.p0').removeClass('p0');
						//$('.top0').removeClass('top0');
						// $('.jsOrder1click').remove();


						/**
						 * Запускаем расшириную Аналитику () для завершенного заказа в 1клик
						 * !!! Должно исполняться после выполнения $.append()
						 */
						OC_MVM.AnaliticsCompleteExtra();

					},
					error: function( jqXHR, textStatus ) {
						self.formStatus('typing');
						return;
					}
				});
			};


		self.AnalyticsFormOpen = function() {
			console.log('% Oneclick. Form is open! ### Begin of AnalyticsFormOpen.');

			var toKISS_oc = { // KISS
				'Checkout Step 1 SKU Quantity': self.quantity(),
				'Checkout Step 1 SKU Total': self.price * self.quantity(),
				'Checkout Step 1 Order Total': self.price * self.quantity() + self.chosenDlvr().price * 1,
				'Checkout Step 1 Order Type': 'one click order'
			}

			if ( typeof(yandexCounter) !== 'undefined' ) {
				console.log('% Oneclick. Run yandexCounter');
                yandexCounter.reachGoal('\orders\complete');
			}

			if ( typeof(_gaq) !== 'undefined' ) {
				console.log('% Oneclick. Setting GA code: /order_form');
				_gaq.push(['_trackEvent', 'QuickOrder', 'Open']);
				_gaq.push(['_trackPageview', '/order_form']);
			}

			if ( typeof(_kmq) !== 'undefined' ) {
				console.log('% Oneclick. Setting toKISS_oc code');
				//console.log(toKISS_oc);
				_kmq.push(['record', 'Checkout Step 1', toKISS_oc]);
			}

			console.log('% Oneclick. ### End of AnalyticsFormOpen');
		};



		self.AnalyticsComplete = function AnalyticsComplete( data ) {
			console.log('% Oneclick. Order is complete! ### Begin of AnalyticsComplete.');
			var phonemask = $('#phonemask'),
				phoneNumber = ( phonemask && phonemask.val() ) ? ( '8' + phonemask.val().replace(/\D/g, "") ) : null,
				toKISS_data,
				toFLK_order
				; // end of vars

			/**
			 * Flocktory (No Analytics=) )
			 */
			if ( typeof(Flocktory) !== 'undefined' )  {
				toFLK_order = {
					"order_id": data.orderNumber,
					"price": self.price * self.quantity() * 1 + self.chosenDlvr().price * 1,
					"items": [{
						"id": self.shortcut,
						"title": self.title,
						"price":  self.price,
						"image": self.icon,
						"count":  self.quantity()
					}]
				};
				Flocktory.popup_opder(toFLK_order);
			}


			/**
			 * KISS Analytics
			 */
			if ((typeof(_kmq) !== 'undefined') && (KM !== 'undefined')) {
				//console.log('phoneNumber');
				//console.log(phoneNumber);
				_kmq.push(['alias', phoneNumber, KM.i()]);
				_kmq.push(['identify', phoneNumber]);

				toKISS_data = {
					'Checkout Complete Order ID':data.orderNumber,
					'Checkout Complete SKU Quantity':self.quantity(),
					'Checkout Complete SKU Total':self.price * self.quantity() * 1,
					'Checkout Complete Delivery Total':self.chosenDlvr().price * 1,
					'Checkout Complete Order Total':self.price * self.quantity() * 1 + self.chosenDlvr().price * 1,
					'Checkout Complete Order Type':'one click order',
					'Checkout Complete Delivery':self.chosenDlvr().type
				};
				_kmq.push(['record', 'Checkout Complete', toKISS_data]);

				toKISS_data = {
					'Checkout Complete SKU':data.productArticle,
					'Checkout Complete SKU Quantity':self.quantity() * 1,
					'Checkout Complete SKU Price':self.price * 1,
					'Checkout Complete Parent category':data.productCategory[0].name,
					'Checkout Complete Category name':data.productCategory[data.productCategory.length-1].name,
					'_t':KM.ts() +  1  ,
					'_d':1
				};
				_kmq.push(['set', toKISS_data]);
			}


			/**
			 * GoogleAnalytics OQuickOrder Success
			 */
			if( typeof(_gaq) !== 'undefined' ) {
				console.log('% Oneclick. Setting GA code: /thanks_form');
				//_gaq.push(['_trackEvent', 'QuickOrder', 'Success', '']);
				_gaq.push(['_trackEvent', 'QuickOrder', 'Success']);
				_gaq.push(['_trackPageview','/thanks_form' ]);
				_gaq.push(['_trackTrans']);
			}

			ANALYTICS.parseAllAnalDivs( $('.jsanalytics') ); // NB! .jsanalytics, добавленные ajax-om не будут парситься // Вероятнее всего, это не нужно, т.к. parseAllAnalDivs не обработает диви, добавленные аджаксом
			ANALYTICS.adriverOrder( {order_id: data.orderNumber} );

			console.log('% Oneclick. ### End of AnalyticsComplete.');
		};

		/**
		 * Расширенная аналитика оформления заказа в 1клик
		 * запускается только после успешного ajax ответа
		 *
		 * @constructor
		 */
		self.AnaliticsCompleteExtra = function AnaliticsCompleteExtra() {
			console.log('% Oneclick. Complete. # Begin of AnaliticsCompleteExtra');
			var analyticsData;

			if ( typeof(_gaq) !== 'undefined' ) {
				analyticsData = $('#GA_addTransJS').data('vars');
				if ( analyticsData ) {
					console.log('% Oneclick. Complete. GA_addTransJS');
					console.log(analyticsData);
					_gaq.push(analyticsData);
				}

				analyticsData = $('#GA_addItemJS').data('vars');
				if ( analyticsData ) {
					console.log('% Oneclick. Complete. GA_addItemJS');
					_gaq.push(analyticsData);
				}
			}

			analyticsData = $('#YA_paramsJS').data('vars');
			if ( analyticsData && typeof(yandexCounter) !== 'undefined' ) {
				console.log('% Oneclick. Complete. YA_paramsJS');
                yandexCounter.reachGoal('QORDER', analyticsData);
			}

			analyticsData = $('#adBelnderJS').data('vars');
			if ( analyticsData && typeof(window.adBelnder) != 'undefined' ) {
				console.log('% Oneclick. Complete. adBelnderJS');
				window.adBelnder.addOrder(analyticsData);
			}

			console.log('% Oneclick. Complete. # End of AnaliticsCompleteExtra');
		};
	} // OCMVM
	
	/* StockViewModel */
	function StockViewModel() {

		var self = this;
		self.showMap = ko.observable(false);
		
		self.title     = Model.jstitle;
		self.price     = Model.jsprice;
		self.icon      = Model.jssimg;
		self.shortcut  = Model.jsshortcut;
		self.region    = Model.jsregion;
		self.today = ko.observable(true);
		self.todayLabel = ko.observable('Сегодня');
		self.tomorrowLabel = ko.observable('Завтра');
		
		self.priceTxt = ko.computed(function() {
			return printPrice( self.price );
		}, this);
		
		
		//dyn
		self.shops = Deliveries['self'].shops.slice(0);
		
		self.todayShops = [];
		self.tomorrowShops = [];
		self.activeCourier = Deliveries.length > 1;
		
		parseDateShop = function( numbers, label ) {
			var out = [];
levup:			for(var i = 0, l = numbers.length; i < l; i++){
					for(var j=0, len = self.shops.length; j < len; j++){
						if( self.shops[j].id == numbers[i] ) {
							var tmp = {};

							for (var prop in self.shops[j] ) {
								tmp[prop] = self.shops[j][prop];
							}

							tmp['lbl'] = label;
							out.push( tmp );
							continue levup;
						}
					}
				}
			return out;
		};

		//find today index
		var tind = 0;

		self.todayShops = parseDateShop( Deliveries['self'].dates[ 0 ].shopIds, 'td' );
		self.todayLabel( Deliveries['self'].dates[ 0 ].name.match(/\d{2}\.\d{2}\.\d{4}/)[0] );
		if( Deliveries['self'].dates.length > 1 ) {
			self.tomorrowShops = parseDateShop( Deliveries['self'].dates[ 1 ].shopIds, 'tmr' );
			self.tomorrowLabel( Deliveries['self'].dates[ 1 ].name.match(/\d{2}\.\d{2}\.\d{4}/)[0] );
		}
		self.pickedShop = ko.observable( self.todayShops[0] );
		self.selectedS = ko.observable( {} );

		var ending = 'ах';

		if( self.todayShops.length % 10 === 1 && self.todayShops.length !== 11 ){
			ending = 'е';
		}

		self.todayH2 = 'Можно забрать <span class="mLft">'+ Deliveries['self'].dates[ 0 ].name +'</span> в '+ self.todayShops.length + ' магазин'+ ending +':';

		if( self.tomorrowShops.length % 10 === 1 && self.tomorrowShops.length !== 11 ) {
			ending = 'е';
		}
		else {
			ending = 'ах';
		}

		self.tomorrowH2 = ( self.todayShops.length > 0 ) ? 'или' : 'Можно забрать ';

		if( Deliveries['self'].dates.length > 1 ){
			self.tomorrowH2 += ' <span class="mRt">'+ Deliveries['self'].dates[ 1 ].name +'</span> в '+ self.tomorrowShops.length + ' магазин'+ ending +':';
		}
		
		self.toggleView = function( flag ) {		
			self.showMap( flag );
			
			if( flag ) {
				if( !self.todayShops.length ) {
					self.toggleTerm( false );
				}
				else{
					self.showMarkers();
				}
			}
			return false;
		};
		
		self.toggleTerm = function( flag ) {
			self.today( flag );
			window.regionMap.hideInfobox();
			self.showMarkers();
			return false;
		};
		
		self.chooseShop = function( item, today ) {
			self.selectedS( item );
			self.today( today );
		};
		
		self.chooseShopById = function( shopnum ) {
			for(var i = 0, l = self.shops.length; i < l; i++) {
				if( self.shops[i].id == shopnum ) {
					self.selectedS( self.shops[i] );
					break;
				}
			}
			self.reserveItem();
		};

		self.pickShopOnMap = function( shid ) {
			for(var i=0, l=self.shops.length; i<l; i++){
				if( self.shops[i].id == shid ) {
					self.pickedShop( self.shops[i] );
					return;
				}
			}
		};

		self.showMarkers = function() {
			var markersPull = {},
				tmp = self.today() ? self.todayShops : self.tomorrowShops;
			//end of vars
			
			for(var i=0, l = tmp.length; i<l; i++) {
				var key = tmp[i].id + '';
				markersPull[ key ] = {
					id: tmp[i].id,
					name: tmp[i].address,
					regtime: tmp[i].regtime,
					latitude: tmp[i].latitude,
					longitude: tmp[i].longitude
				};
			}

			window.regionMap.showMarkers( markersPull );
		};
		
		self.reserveItem = function() {
			// console.log(tind)
			// console.log(Deliveries['self'].dates[ tind ])
			var MVMinterface = {
				type: 'self',
				date: self.today() ? Deliveries['self'].dates[ tind ] : Deliveries['self'].dates[ tind + 1 ],
				shop: self.selectedS()
			};

			OC_MVM.preparedData( MVMinterface );
			// OC_MVM.pickShop(selectedS())
			$('#order1click-container-new').lightbox_me( { } );
			return false;
		};
		
		self.onlyCourier = function() {
			var MVMinterface = {
				type: 'courier'
			};
			OC_MVM.preparedData( MVMinterface );
			$('#order1click-container-new').lightbox_me( { } );
			return false;
		};
			
	} //StockViewModel	
			
	/////////////////////////////////////////
	

	/* Inputs */
	function enableHandlers() {
		$("#phonemask").parent().prepend('<span id="phonePH">+7</span>');

		if( typeof( $.mask ) !== 'undefined' ) {
			$.mask.definitions['n'] = '[0-9]';
			$("#phonemask").mask("(nnn) nnn-nn-nn");

			if( $("#phonemask")[0].getAttribute('value') ){
				$("#phonemask").val( $("#phonemask")[0].getAttribute('value') );
			}

			$.mask.definitions['*'] = "[0-9*]";
			$("#scCard").mask("* ****** ******", { placeholder: "*" } );

			if( $("#scCard")[0].getAttribute('value') ){
				$("#scCard").val( $("#scCard")[0].getAttribute('value') );
			}

			$("#scCard").blur( function() {
				if( $(this).val() === "* ****** ******" ) {
					$(this).trigger('unmask').val('');
					$(this).focus( function() {
						$("#scCard").mask('2 98nnnn nnnnn', {
							placeholder: '*'
						});
					});
				}
			});
		}
	}

	/* One Click Order */
	if( $('.jsOrder1click').length ) {
		MapInterface.ready( 'yandex', { 
			yandex: $('#map-info_window-container-ya'), 
			google: $('#map-info_window-container')
		});

		var Model = $('.jsOrder1click').data('model'),
			inputUrl = $('.jsOrder1click').attr('link-input'),
			outputUrl = $('.jsOrder1click').attr('link-output'),
            subscribeWrapper = $('.bSubscibeWrapper'),
            subscibeCheckboxEnabled;
		//end of vars

        $('body').on('userLogged', function( event, userInfo ) {
            /**
             * Email-Подписка
             * Если юзер уже подписан, не нужно отображать чекбокс с предложением подписаться
             */
            if ( userInfo && (false === userInfo.isSubscribed) ) {
                subscribeWrapper.show();
                subscibeCheckboxEnabled = true;
                console.log('НЕ скрываем, даже показываем блок подписки.');
            } else {
                subscribeWrapper.hide();
                subscibeCheckboxEnabled = false;
                console.log('Скрываем блок подписки, т.к. юзер уже подписан либо незарегистрирован.');
            }
        });
		
		Deliveries = { // zaglushka
			'self': {
				modeId: 4,
				name: 'Доставка',
				price: 400,
				dates: [ {value: '10-02-2012', name: '10 февраля'}, {value: '11-02-2012', name: '11 февраля'} ]

			}
		};
		
		var selfAvailable = false;  //'self' in Deliveries
		
		/* Load Data from Server */
		oneClickIsReady = false;
		var postData = {
			product_id: Model.jsitemid,
			product_quantity: 1,
			region_id: Model.jsregionid * 1
		};

		var updateIWCL = function ( marker ) {
			if( typeof(OC_MVM) !== 'undefined' ){
				OC_MVM.pickShopOnMap( marker.id );
			}
		};


		var shopListErrorHandler = function shopListErrorHandler() {
			$('.jsOrder1click').remove();
		};

		/**
		 * Обработка данных о списке магазинов с сервера
		 * 
		 * @param	{Object}	data	Ответ от сервера
		 */
		var shopListSuccessHandler = function shopListSuccessHandler( data ) {
			if( !data.success || data.data.length === 0 ) {
				$('.jsOrder1click').remove();
				return false;
			}

			Deliveries = data.data;

			console.info('data recive');
			selfAvailable = 'self' in Deliveries || 'now' in Deliveries;
			console.log(selfAvailable);

			if ( selfAvailable ) {
				if ( Deliveries.hasOwnProperty('self') ) {
					console.log('self shops');
					mapCenter = calcMCenter( Deliveries['self'].shops );
				}
				else {
					console.log('now shops');
					mapCenter = calcMCenter( Deliveries['now'].shops );
				}
			}

			OC_MVM = new OneCViewModel();
			ko.applyBindings( OC_MVM, $('#order1click-container-new')[0] ); // this way, Lukas!
			
			if ( selfAvailable ) {
				var mapCallback = function() {
					window.regionMap.addHandler( '.shopchoose', pickStoreMVMCL );
				};

				try {
					console.info('грузим карту');
					MapInterface.init( mapCenter, 'mapPopup', mapCallback, updateIWCL );
				}
				catch( e ) {
					console.warn('карта не загрузилась');
					$('.bFast__eMapLink').remove();
				}
			}

			oneClickIsReady = true;
			enableHandlers();

			/**
			 * Открытие окна, если есть хэш oneclick
			 *
			 * https://jira.enter.ru/browse/SITE-1778
			 */
			if ( document.location.hash.match(/oneclick/) ) {
				$('.jsOrder1click').trigger('click');
			}
		}

		$.ajax({
			type: 'POST',
			url: inputUrl,
			data: postData,
			success: shopListSuccessHandler,
			statusCode: {
				500: shopListErrorHandler,
				502: shopListErrorHandler,
				503: shopListErrorHandler,
				504: shopListErrorHandler
			},
			error: shopListErrorHandler
		});

		var pickStoreMVMCL = function ( node ) {
			var shopnum = $(node).parent().find('.shopnum').text();

			OC_MVM.toggleMap();
			OC_MVM.chooseShopById( shopnum );
		};

		$('.jsOrder1click').bind('click', function(e) { // button 'Купить в один клик'
			if( !oneClickIsReady ){
				return false;
			}

			OC_MVM.AnalyticsFormOpen();

			var handleSubscibeWrapper = function () {
				var value = $('#recipientEmail').val(),
					checkbox = $('input[type="checkbox"][name="subscribe"]'),
					bSubscibeWrapper = $('#recipientEmail').siblings('.bSubscibeWrapper'),
					bSubscibe = $('.bSubscibe'),
					recipientEmail = $('#recipientEmail');
				if ( !value && $('#recipientEmail').siblings('.mEmpty').length ) {
					recipientEmail.siblings('.mEmpty').hide();
				}
				if ( value && value.isEmail() && bSubscibeWrapper.hasClass('hf') ) {
					bSubscibeWrapper.removeClass('hf');

					// Если юзер уже подписан, не нужно обрабатывать чекбокс с предложением подписаться,
					if ( subscibeCheckboxEnabled ) {
						checkbox.attr('disabled', '');
						checkbox.attr('checked', 'checked')
						checkbox.val(1)
						bSubscibe.addClass('checked');
					}

					recipientEmail.siblings('.mEmpty').hide();
				} else if ( ( !value || value && !value.isEmail() ) && !bSubscibeWrapper.hasClass('hf') ) {
					bSubscibeWrapper.addClass('hf');

					// Если юзер уже подписан, не нужно обрабатывать чекбокс с предложением подписаться,
					if ( subscibeCheckboxEnabled ) {
						checkbox.attr('disabled', 'disabled');
						checkbox.attr('checked', '')
						checkbox.val(0)
						bSubscibe.removeClass('checked');
					}

					if ( recipientEmail.val() ) {
						recipientEmail.siblings('.mEmpty').show();
					}
				}
			};

			/**
			 * Email-Подписка
			 */
			$('body').on('keyup ready', '#recipientEmail', function () {
				handleSubscibeWrapper();
			});

			$('#order1click-container-new').lightbox_me({
				centered: true,
				onClose: function() {
					if( 'regionMap' in window ){
						window.regionMap.closeMap( OC_MVM.turnOffMap );
					}
				}
			});
			return false;
		});

        $('.jsOrder1clickProxy').bind('click', function(e) { // button 'Резерв'
            $('.jsOrder1click').click();

            e.preventDefault();
        });
		
	} // One Click Order

	/* Page 'Where to buy?' , Stock Map */
	
	if( $('#stockBlock').length ) {
		MapInterface.ready( 'yandex', { 
			yandex: $('#infowindowforstockYa'), 
			google: $('#infowindowforstock')
		});

		var Model     = $('#stockmodel').data('value'),
			inputUrl  = $('#stockmodel').attr('link-input'),
			outputUrl = $('#stockmodel').attr('link-output'),
			selfAvailable = false,
			currentDate = (new Date()).toISOString().substr(0,10),
			/* Load Data from Server */
			postData = {
				product_id: Model.jsitemid,
				product_quantity: 1,
				region_id: Model.jsregionid*1
			};
		//end of vars
		
		$.post( inputUrl, postData, function(data) {
			if( !data.success ) {
				//SHOW WARNING, NO MVM
				$('.bOrderPreloader').hide();
				$('#noDlvr').show();
				return false;
			}

			Deliveries = data.data;
			var le = 0;

			for(var key in Deliveries){
				le++;
			}

			Deliveries.length = le;

			if( 'currentDate' in data ){
				if( data.currentDate != '' ){
					currentDate = data.currentDate;
				}
			}

			$('.bOrderPreloader').hide();

			if( le === 0 ) {
				//SHOW WARNING, NO MVM
				$('#noDlvr').show();
				return false;
			}

			selfAvailable = 'self' in Deliveries;

			if( !selfAvailable ) {
				//SHOW WARNING, NO SELF DELIVERY
				$('#noDlvr').show();
				return false;		
			}

			if( !(Deliveries['self'].dates.length > 0) ) {
				//SHOW WARNING, NO TODAY AND TOMORROW DELIVERY
				$('#noDlvr').show();
				return false;		
			}

			if( selfAvailable ) {
				mapCenter = calcMCenter( Deliveries['self'].shops );
			}

			MVM = new StockViewModel();
			ko.applyBindings( MVM , $('#stockCntr')[0] ); // this way, Lukas!
			
			OC_MVM = new OneCViewModel();
			ko.applyBindings( OC_MVM, $('#order1click-container-new')[0] ); // this way, Lukas!
			enableHandlers();

			if( selfAvailable ) {
				var pickStoreMVM = function ( node ) {	
						var shopnum = $(node).parent().find('.shopnum').text();
						MVM.chooseShopById( shopnum );
					},

					updateIW = function ( marker ) {
						if( typeof(MVM) !== 'undefined' ){
							MVM.pickShopOnMap( marker.id );
						}
					},

					mapCallback = function() {
						window.regionMap.addHandler( '.shopchoose', pickStoreMVM );
					};

				MapInterface.init( mapCenter, 'stockmap', mapCallback, updateIW );
			}


			$('#stockBlock').show();
		});
	} // Page 'Where to buy?'
});	