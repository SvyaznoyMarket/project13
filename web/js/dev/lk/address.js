$(function(){

    var
        $body = $('body'),
        $mainContainer = $('#personal-container'),
        $deleteAddressPopupTemplate = $('#tpl-user-deleteAddressPopup'),
        $form = $('.js-userAddress-form'),

        region = $('#page-config').data('value').user.region,
        kladrConfig = $('#kladr-config').data('value'),

        showPopup = function(selector) {
            $('body').append('<div class="overlay"></div>');
            $('.overlay').data('popup', selector).show();
            $(selector).show();
        },

        hidePopup = function(selector) {
            $(selector).remove();
            $('.overlay').remove();
        },

        initKladr = function() {
            var
                query = $.extend(kladrConfig, {'limit': 1, type: $.kladr.type.city, name: region.name})
            ;

            $.kladr.api(query, function (data){
                var id = (data[0] && data[0].id) ? data[0].id : 0;
                if (0 == id) {
                    console.error('КЛАДР не определил город, конфигурация запроса: ', query);
                }
            })
        },

        onAutocompleteResponse = function(request, response) {
            var
                $el = $(this),
                url = $el.data('url'),
                type = $el.data('field'),
                query
            ;

            if (url && url.length) {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    data: {
                        q: request.term
                    },
                    success: function(result) {
                        var data = result.data ? result.data.slice(0, 15) : [];

                        response($.map(data, function (item) {
                            return { label: item.name, value: {id: item.kladrId, name: item.name, regionId: item.id} };
                        }));
                    }
                });
            } else {
                query = $.extend({}, { limit: 10, name: request.term }, getParent($el));
                console.log('kladr.query.request', query);

                $.kladr.api(query, function (result) {
                    console.log('kladr.query.response', result);

                    response($.map(result, function (item) {
                        return { label: (type == 'street' ? item.name + ' ' + item.typeShort + '.' : item.name), value: item };
                    }));
                });
            }
        },

        getParent = function($el) {
            var
                type = $el.data('field'),
                parentType = $el.data('parent-field'),
                parentId = $el.data('parent-kladr-id')
            ;

            console.info('parent', { type: $.kladr.type[type], parentType: parentType, parentId: parentId });
            return { type: $.kladr.type[type], parentType: parentType, parentId: parentId };
        },

        onInputFocused = function () {
            var
                $el = $(this),
                type = $el.data('field'),
                relations = $el.data('relation'),
                $form = $(relations['form'])
            ;

            $el.autocomplete(
                {
                    source: onAutocompleteResponse.bind($el),
                    minLength: 1,
                    open: function(event, ui) {},
                    select: function(event, ui) {
                        console.info('autocomplete.select', 'ui.item.value', ui.item.value);

                        // sets value
                        $el.val(ui.item.label);
                        // sets parent kladr id
                        $form.find('[data-parent-field="' + type  + '"]').data('parent-kladr-id', ui.item.value.id);
                        // sets hidden input values
                        $form.find('[data-field="zipCode"]').val(ui.item.value.zip);
                        if ('city' === type) {
                            $form.find('[data-field="regionId"]').val(ui.item.value.regionId);
                        }
                        if ('street' === type) {
                            $form.find('[data-field="streetType"]').val(ui.item.value.typeShort);
                        }
                        if (
                            !$form.find('[data-field="building"]').val()
                            || ('building' === type)
                        ) {
                            $form.find('[data-field="kladrId"]').val(ui.item.value.id)
                        }

                        return false;
                    },
                    focus: function(event, ui) {
                        this.value = ui.item.label;
                        event.preventDefault();
                        event.stopPropagation();
                    },
                    change: function(event, ui) {},
                    messages: {
                        noResults: '',
                        results: function() {}
                    }
                }
            )
            .data('ui-autocomplete')._renderMenu = function(ul, items) {
                $.each(items, function(index, item) {
                    this._renderItemData(ul, item);
                }.bind(this));
                if ('street' === $el.data('field')) {
                    ul.addClass('ui-autocomplete-street');
                } else {
                    ul.addClass('ui-autocomplete-house-or-apartment');
                }
            };
        }
    ;

    $body.on('focus', '.js-user-address', onInputFocused);

    initKladr();

    $body.on('click', '.overlay', function() {
        var selector = $(this).data('popup');
        hidePopup(selector);
    });
    $body.on('click', '.js-modal-close', function() {
        hidePopup('#' + $(this).closest('.js-modal').attr('id'))
    });

    // удалить адрес
    $body.on('click', '.js-user-deleteAddress', function() {
        var
            $el = $(this),
            data = $el.data(),
            templateValue = data.value,
            $popup
        ;

        try {
            $popup = $(Mustache.render($deleteAddressPopupTemplate.html(), templateValue)).appendTo($mainContainer);
            showPopup('#' + $popup.attr('id'));
        } catch (error) {
            console.error(error);
        }
    });

    // не отправлять форму по нажатию на ENTER
    $form.find('[data-field]').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
        }
    });
});
