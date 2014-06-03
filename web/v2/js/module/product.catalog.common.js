define(
    [
        'jquery',
        'module/config', 'jquery.ui', 'jquery.photoswipe'
    ],
    function ($, config) {
        var $slider = $('.js-rangeSlider-container');

        $slider.each(function(i, el) {
            var $el = $(el),
                $slider = $el.find('.js-rangeSlider'),
                $fromInput = $el.find('.js-rangeSlider-from'),
                $toInput = $el.find('.js-rangeSlider-to'),
                dataValue = $el.data('value'),

                updateInput = function(e) {
                    var value = '0' + $(this).val();

                    value = parseFloat(value);
                    value =
                        (value > dataValue.max) ? dataValue.max :
                        ( value < dataValue.min ) ? dataValue.min :
                        value;

                    $(this).val(value);

                    $slider.slider({
                        values: [
                            $fromInput.val(),
                            $toInput.val()
                        ]
                    });
                }
            ;

            if (!dataValue || !$slider.length) {
                console.warn('slider', $el, dataValue);
                return true;
            } else {
                console.info('slider', $el, dataValue);
            }

            $slider.slider({
                range: true,
                step: dataValue.step,
                min: dataValue.min,
                max: dataValue.max,
                values: [
                    $fromInput.val(),
                    $toInput.val()
                ],

                slide: function(e, ui) {
                    $fromInput.val(ui.values[0]);
                    $toInput.val(ui.values[1]);
                },

                change: function(e, ui) {
                    if (e.originalEvent) {
                        console.info('js-rangeSlider', $slider, ui, e);

                        if (ui.value == ui.values[0]) {
                            $fromInput.trigger('change');
                        }
                        if (ui.value == ui.values[1]) {
                            $toInput.trigger('change');
                        }
                    }
                }
            });

            $fromInput.on('change', updateInput);
            $toInput.on('change', updateInput);
        });

		var paramsBtn = $('.js-action-params'),
			params = $('.params'),

			paramsTitle = $('.params_title'),
			paramsCont = $('.params_cont');
		// end of vars
		
		var paramsAction = function paramsAction( event ) {

            var paramsBtnFixed = $('.paramsBtnFixed');

			event.preventDefault();

			params.toggleClass('params-open');
			paramsTitle.removeClass('params_title-open');
			paramsCont.removeClass('params_cont-open');

            paramsBtnFixed.toggleClass('paramsBtnFixed-show');

            var w = $(window),
            scrollTarget,
            scrollTargetOffset;

            var checkScroll = function checkScroll() {
                var
                    nowScroll = w.scrollTop();
                    nowScrollHeight = w.height();
                // end of vars

                if ( nowScroll <= Math.abs(nowScrollHeight - scrollTargetOffset) ) {
                    $('.paramsBtnFixed').addClass('paramsBtnFixed-show');
                }
                else {
                    $('.paramsBtnFixed').removeClass('paramsBtnFixed-show');
                }
            };

            if ( $('.paramsBtnFixed').length ) {
                scrollTarget = $('#productCatalog-filter-form');

                if ( scrollTarget.length ) {
                    scrollTargetOffset = scrollTarget.offset().top;
                    w.on('scroll', checkScroll);
                }
            }
		},

		paramsItem = function paramsItem() {

			var $self = $(this);

			$self.toggleClass('params_title-open').next('.params_cont').toggleClass('params_cont-open');
		};
		// end of functions
        
        

		paramsBtn.on('click', paramsAction);
		paramsTitle.on('click', paramsItem);
    }
);