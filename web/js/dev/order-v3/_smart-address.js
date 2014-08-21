;(function($) {

    function inputAddress(){
        var config = $('#kladr-config').data('value'),
            $addressBlock = $('.orderCol_addrs'),
            $input = $addressBlock.find('input'),
            $inputPrefix = $addressBlock.find('#addressInputPrefix'),
            typeNames = {
                street: 'Улица',
                building: 'дом',
                apartment: 'квартира'
            },
            spinner = typeof Spinner == 'function' ? new Spinner({
                lines: 7, // The number of lines to draw
                length: 3, // The length of each line
                width: 3, // The line thickness
                radius: 2, // The radius of the inner circle
                corners: 1, // Corner roundness (0..1)
                rotate: 0, // The rotation offset
                direction: 1, // 1: clockwise, -1: counterclockwise
                color: '#666', // #rgb or #rrggbb or array of colors
                speed: 1, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: true, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: '50%', // Top position relative to parent
                left: '50%' // Left position relative to parent
            }) : null,
            address, init, autocompleteRequest, spinnerBlock;

        if ($input.length === 0) return;

        spinnerBlock = $('<div />', {'class':'kladr_spinner'}).css({'position': 'absolute', top: 0, right: 0, height: '30px', width: '30px'});
        $addressBlock.prepend(spinnerBlock);

        function Address(c) {
            this.city = c;
            this.street = {};
            this.building = {};
            this.apartment = {};

            this.getParent = function() {
                console.log('getParent()');
                if (this.street.id && !this.building.name) return { parentType: this.street.contentType, parentId: this.street.id, type: $.kladr.type.building };
                if (this.city.id && !this.street.name) return { parentType: this.city.contentType, parentId: this.city.id };
                else return false;
            };

            this.getLastType = function() {
                console.log('getLastType()', this);
                if (typeof this.street.name === 'undefined') return 'street';
                else if (typeof this.building.name === 'undefined') return 'building';
                else if (typeof this.apartment.name === 'undefined') return 'apartment';
                else return false;
            };

            this.getNextType = function() {
                console.log('getNextType()', this);
                console.log('typeof this.street.name', typeof this.street.name);

                if (typeof this.building.name !== 'undefined') return 'apartment';
                else if (typeof this.street.name !== 'undefined') return 'building';
                else if (typeof this.street.name === 'undefined') return 'street';
                else return false;
            };

            this.update = function(item) {
                if (typeof item.contentType === 'undefined') {
                    if (item.type === false) {
                        console.error('False type in address update', item);
                        return;
                    }
                    item.contentType = item.type;
                }
                console.log('update(), contentType', item.contentType);
                this[item.contentType] = item;
                $input.autocomplete('close').val('');
                addAddressItem(item);
                updatePrefix($('input:focus').eq(0));
                if (item.contentType == 'apartment') $input.hide();
                console.log('Address update: address, item', this, item);
                ENTER.OrderV3.address = this;
            };

            this.clear = function(til, elem) {
                var $elem = $('.jsAddressItem[data-type='+til+']');
                switch (til) {
                    case 'apartment': this.apartment = {}; break;
                    case 'building' : this.apartment = {}; this.building = {}; break;
                    case 'street'   : this.apartment = {}; this.building = {}; this.street = {}; break;
                }
                $elem.nextAll('.jsAddressItem').remove();
                if (elem) $(elem).closest('.orderCol_addrs').find('input').val($elem.find('.jsAddressItemName').eq(0).text()).show().focus();
                $elem.remove();
                console.log('Address cleared til %s', til, this);
            };

            this.clearLast = function(elem) {
                var lastType = $('.jsAddressItem:last').data('type');
                console.log('lastType', lastType);
                if (lastType) this.clear(lastType, elem);
            };

            return this;
        }

        function saveAddressRequest() {
            $.ajax({
                type: 'POST',
                data: {
                    'action' : 'changeAddress',
                    'params' : { street: address.street.type + ' ' + address.street.name, building: address.building.name, apartment: address.apartment.name }
                }
            }).fail(function(jqXHR){
                var response = $.parseJSON(jqXHR.responseText);
                if (response.result) {
                    console.error(response.result);
                }
            }).done(function(data){
                console.log("Query: %s", data.result.OrderDeliveryRequest);
                console.log("Model:", data.result.OrderDeliveryModel);
                console.log('Address saved');
            })
        }

        function updatePrefix(elem) {
            var type = address.getLastType(),
                $prefixHolder = $(elem).siblings('#addressInputPrefix');
            $prefixHolder.text(typeNames[type] + (type == 'apartment' ? ' (необязательно)' : '') + ":");
        }

        /**
         * Генерация HTML
         * @param item
         */
        function addAddressItem(item) {
            var typeName,
                holder = $('<li />', {
                    "class": "orderCol_addrs_fld_i jsAddressItem",
                    "data-item": JSON.stringify(item),
                    "data-type": item.contentType
                });


            typeName = typeof item.id !== 'undefined' ? item.type : typeNames[item.contentType];

            holder.append($('<span />').addClass('orderCol_addrs_fld_n jsAddressItemType').text(typeName)).
                append($('<span />').addClass('orderCol_addrs_fld_val jsAddressItemName').text(item.name));

            holder.insertBefore($input.parent());
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

        function fillAddressBlock(address) {
            $.each(['street', 'building', 'apartment'], function (i,val){
                if (typeof address[val].type !== 'undefined' || typeof address[val].contentType !== 'undefined' ) addAddressItem(address[val]);
            });
        }

        // Удаление пунктов по клику на адресе
        $addressBlock.on('click', '.jsAddressItem', function(e) {
            e.stopPropagation();
            var type = $(this).data('type');
            address.clear(type, this);
        });

        // Клик по блоку адреса
        $addressBlock.on('click', function(e) {
            if (address.getLastType() !== false) {
                $(this).find('input').eq(0).show().focus();
            }
            e.preventDefault();
        });



        /**
         * Запрос к КЛАДР API
         * @param request
         * @param response
         */
        autocompleteRequest = function autoCompleRequestF (request, response) {
            if (address.getParent() !== false) {
                var query = $.extend(config, { limit: 10, type: $.kladr.type.street, name: request.term }, address.getParent());
                if (spinner) spinner.spin($('.kladr_spinner')[0]);
                console.log('[КЛАДР] запрос: ', query);
                $.kladr.api(query, function (data) {
                    console.log('[КЛАДР] ответ', data);
                    if (spinner) spinner.stop();
                    response($.map(data, function (elem) {
                        return { label: formatStreetName(elem) , value: elem }
                    }))
                });
            }
        };

        $input.autocomplete({
//            appendTo: '#kladrAutocomplete',
            source: autocompleteRequest,
            minLength: 1,
            select: function( event, ui ) {
                this.value = '';
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

        $input.on({
            focus: function(){
                updatePrefix(this)
            },
            blur: function(){
                $inputPrefix.text('');
                saveAddressRequest()
            }
        });

        // заполнение адреса по нажатию Enter
        $input.on('keypress', function(e){
            console.log(address.getNextType());
            if (e.which == 13) {
                console.log('Enter pressed, address: ', address);
                if ($(this).val().length > 0) address.update({type: address.getNextType(), name: $(this).val()})
            }
        });

        // обработка Backspace
        $input.on('keydown', function(e) {
            var key = e.keyCode || e.charCode;
            if (key === 8 && $(this).val().length === 0) {
                address.clearLast(this);
                e.preventDefault();
            }

        });

        /**
         * Рендеринг меню автокомплита
         * @param ul
         * @param items
         * @private
         */
        $input.data('ui-autocomplete')._renderMenu = function( ul, items ) {
            var that = this;
            $.each( items, function( index, item ) {
                that._renderItemData( ul, item );
            });
        };

        /**
         * Рендеринг элемента списка автокомплита
         * @param ul
         * @param item
         * @returns {*}
         * @private
         */
        $input.data('ui-autocomplete')._renderItem = function( ul, item ) {
            return $( "<li>" )
                .attr( "data-value", JSON.stringify(item.value) )
                .append( $( "<a>" ).text( item.label ) )
                .appendTo( ul );
        };

        /**
         * Инициализация: запрос города для дальнейшего поиска адреса
         */
        init = function initF() {
            if (typeof ENTER.OrderV3.address === 'object') {
                address = ENTER.OrderV3.address;
                fillAddressBlock(address);
                $input.hide();

            } else {
                console.log('spinner', spinner);
                if (spinner) spinner.spin($('.kladr_spinner')[0]);
                address = new Address({});
                console.log('Определение адреса КЛАДР, запрос', $.extend(config, {limit: 1, type: $.kladr.type.city, name: $('#region-name').data('value')}));
                $.kladr.api($.extend(config, {limit: 1, contentType: $.kladr.type.city, query: $('#region-name').data('value')}), function (data){
                    console.log('KLADR data', data);
                    var id = data.length > 0 ? data[0].id : 0;
                    if (id==0) console.error('КЛАДР не определил город, конфигурация запроса: ', $.extend(config, {limit: 1, type: $.kladr.type.city, name: $('#region-name').data('value')}));
                    else address.city = data[0];
                    if (spinner) spinner.stop()
                })
            }
        };

        init();
    }

    ENTER.OrderV3.constructors.smartAddress = inputAddress;

    inputAddress();

    //$(document).ajaxComplete(inputAddress);

}(jQuery));