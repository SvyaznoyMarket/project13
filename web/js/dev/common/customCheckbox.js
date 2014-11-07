/**
 * Custom inputs
 *
 * @requires jQuery
 *
 * @author	Zaytsev Alexandr
 */
;(function() {
	var inputs = $('input.bCustomInput, .js-customInput'),
		body = $('body');
	// end of vars

	var updateState = function updateState() {
		if ( !$(this).is('[type=checkbox]') && !$(this).is('[type=radio]') ) {
			return;
		}

		var $self = $(this),
			id = $self.attr('id'),
			type = ( $self.is('[type=checkbox]') ) ? 'checkbox' : 'radio',
			groupName = $self.attr('name') || '',
			label = $('label[for="'+id+'"]');
		// end of vars

		if ( type === 'checkbox' ) {

			if ( $self.is(':checked') ) {
				label.addClass('mChecked');
			}
			else {
				label.removeClass('mChecked');
			}
		}


		if ( type === 'radio' && $self.is(':checked') ) {
			$('input[name="'+groupName+'"]').each(function() {
				var currElement = $(this),
					currId = currElement.attr('id');

				$('label[for="'+currId+'"]').removeClass('mChecked');
			});

			label.addClass('mChecked');
		}
	};


	body.on('updateState', '.bCustomInput, .js-customInput', updateState);

	body.on( 'change', '.bCustomInput, .js-customInput', function() {
		$(this).trigger('updateState');
	});

	inputs.trigger('updateState');
}());