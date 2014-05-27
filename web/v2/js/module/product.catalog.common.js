define(
    [
        'jquery',
        'module/config', 'jquery.ui', 'jquery.photoswipe'
    ],
    function ($, config) {
        var $slider = $('.js-rangeSlider');

        $slider.each(function(i, el) {
            var $el = $(el),
                dataValue = $el.data('value')
            ;

            if (!dataValue) {
                console.warn('slider', $el, dataValue);
                return true;
            } else {
                console.info('slider', $el, dataValue);
            }

            $el.slider({
                range: true,
                min: dataValue.min,
                max: dataValue.max,
                values: [ 0, 500 ]
            });
        });

		var paramsBtn = $('.js-action-params'),
			params = $('.params'),

			paramsTitle = $('.params_title'),
			paramsCont = $('.params_cont');
		// end of vars
		
		var paramsAction = function paramsAction( event ) {

			event.preventDefault();

			params.toggleClass('params-open');
			paramsTitle.removeClass('params_title-open');
			paramsCont.removeClass('params_cont-open');
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