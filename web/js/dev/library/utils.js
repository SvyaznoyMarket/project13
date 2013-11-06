;(function ( ENTER ) {
    var utils = ENTER.utils;


    /**
     * Возвращает колчество свойств в объекте.
     *
     * @param       {Object}        obj
     * 
     * @returns     {Number}        count
     */
    utils.objLen = function objLen( obj ) {
        var len = 0,
            p;
        // end of vars

        for ( p in obj ) {
            if ( obj.hasOwnProperty(p) ) {
                len++;
            }
        }

        return len;
    };


}(window.ENTER));