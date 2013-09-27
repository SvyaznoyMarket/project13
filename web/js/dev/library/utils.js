;(function (ENTER) {
    var utils = ENTER.utils;


    /**
     * Возвращает колчество свойств в объекте.
     *
     * @param       {object}        obj
     * @returns     {number}        count
     */
    utils.objLen = function objLen(obj) {
        var len = 0, p;
        for ( p in obj ) {
            if ( obj.hasOwnProperty(p) ) {
                len++;
            }
        }
        return len;
    }


}(window.ENTER));