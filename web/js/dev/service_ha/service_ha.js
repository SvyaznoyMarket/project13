/**
 * Костыль для выбора региона на странице /service_ha
 *
 * @author    Shaposhnik Vitaly
 * @requires  jQuery
 */
;(function($) {
	var
		region_list = $(".entry-content select#region_list"),
		region = $(".jsChangeRegion"),
		regionName = region.length ? region.text() : false;
	// end of vars

	var
		init = function init() {
			region_list = $(".entry-content select#region_list");

			if ( !region_list.length ) {
				return;
			}

			region_list.find('option').each(checkRegion);
		},

		checkRegion = function() {
			var self = $(this);

			if ( self.text() && -1 !== self.text().indexOf(regionName) ) {
				self.prop("selected", true).change();
			}
		};
	// end of functions

	if ( !regionName ) {
		return;
	}

	setTimeout(init, 1500);

})(jQuery);