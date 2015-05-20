console.log('ports.js inited');

$(function(){

    console.groupCollapsed('parseAllAnalDivs');

    $('.jsanalytics').each(function() {

        // document.write is overwritten in loadjs.js to document.writeln
        var $this = $(this);

        console.log($this);

        if ( $this.hasClass('.parsed') ) {
            console.warn('Parsed. Return');
            return;
        }

        document.writeln = function() {
            $this.html( arguments[0] );
        };

        if ( this.id in ANALYTICS ) {
            try {
                // call function
                ANALYTICS[this.id]($(this).data('vars'));
            } catch (e) {
                console.error(e);
            }
        }

        $this.addClass('parsed')
    });

    document.writeln = function() {
        $('body').append( $(arguments[0] + '') );
    };
    console.groupEnd();
});


