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

	function updateInput($input) {
		if ( !$input.is('[type=checkbox]') && !$input.is('[type=radio]') ) {
			return;
		}

		var id = $input.attr('id'),
			type = ( $input.is('[type=checkbox]') ) ? 'checkbox' : 'radio',
			groupName = $input.attr('name') || '',
			label = $('label[for="'+id+'"]');
		// end of vars

		if ( type === 'checkbox' ) {

			if ( $input.is(':checked') ) {
				label.addClass('mChecked');
			}
			else {
				label.removeClass('mChecked');
			}
		}


		if ( type === 'radio' ) {
			if ( $input.is(':checked') ) {
				$('input[name="'+groupName+'"]').each(function() {
					var currElement = $(this),
						currId = currElement.attr('id');

					$('label[for="'+currId+'"]').removeClass('mChecked');
				});

				label.addClass('mChecked');
			} else {
				label.removeClass('mChecked');
			}
		}
	}


	body.on('change', '.bCustomInput, .js-customInput', function(e) {
		updateInput($(e.currentTarget));
	});

	inputs.each(function(index, input) {
		updateInput($(input));
	});
}());