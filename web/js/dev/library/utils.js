;(function (ENTER) {
    var utils = ENTER.utils;


    /**
     * Возвращает колчество элементов в объекте.
     *
     * @param       {object}        obj
     * @returns     {number}        count
     */
    utils.objLen = function objLen(obj) {
        var count = 0, p;
        for ( p in obj ) {
            if ( obj.hasOwnProperty(p) ) {
                count++;
            }
        }
        return count;
    }


}(window.ENTER));