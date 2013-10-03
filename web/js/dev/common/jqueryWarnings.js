/**
 * Предупреждения при вводе текста
 *
 * @author      Shaposhnik Vitaly
 * @requires    jQuery
 */

;(function($) {
    $.fn.warnings = function() {
        var rwn = $('<strong id="ruschars" class="pswwarning">RUS</strong>');

        rwn.css({
            'border': '1px solid red',
            'color': 'red',
            'border-radius': '3px',
            'background-color':'#fff',
            'position': 'absolute',
            'height': '16px',
            'padding': '1px 3px',
            'margin-top': '2px'
        });

        var cln = rwn.clone().attr('id','capslock').html('CAPS LOCK').css('marginLeft', '-78px');

        $(this).keypress(function(e) {
            var s = String.fromCharCode( e.which );

            if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
                if ( !$('#capslock').length ) {
                    $(this).after(cln);
                }
            }
            else {
                if ( $('#capslock').length ) {
                    $('#capslock').remove();
                }
            }
        });

        $(this).keyup(function(e) {
            if( /[а-яА-ЯёЁ]/.test( $(this).val() ) ) {
                if ( !$('#ruschars').length ) {
                    if ( $('#capslock').length ) {
                        rwn.css('marginLeft','-116px');
                    }
                    else {
                        rwn.css('marginLeft','-36px');
                    }
                    $(this).after(rwn);
                }
            }
            else {
                if ( $('#ruschars').length ) {
                    $('#ruschars').remove();
                }
            }
        });
    };
})(jQuery);