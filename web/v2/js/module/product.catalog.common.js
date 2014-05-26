define(
    [
        'jquery',
        'module/config', 'jquery.ui', 'jquery.photoswipe'
    ],
    function ($, config) {
		$( '.js-rangeSlider' ).slider({
			range: true,
			min: 0,
			max: 500,
			values: [ 0, 500 ]
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