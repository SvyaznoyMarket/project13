define(
    [],
    function () {

        return {
            formatCurrency: function (value) {
                return value.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
            },

            addUrlParam: function (url, key, value) {
                var
                    re = new RegExp('([?|&])' + key + '=.*?(&|#|$)(.*)', 'gi'),
                    separator,
                    valueKey,
                    newKey,
                    hash;
                // end of vars

                if (typeof value === 'object') {
                    for (valueKey in value) {
                        if (value.hasOwnProperty(valueKey)) {
                            newKey = key + '[' + valueKey + ']';

                            url = addParam(url, key + '[' + valueKey + ']', value[valueKey]);
                        }
                    }

                    return url;
                }

                if (re.test(url)) {
                    if (typeof value !== 'undefined' && value !== null) {
                        return url.replace(re, '$1' + key + '=' + value + '$2$3');
                    } else {
                        return url.replace(re, '$1$3').replace(/(&|\?)$/, '');
                    }
                } else {
                    if (typeof value !== 'undefined' && value !== null) {
                        separator = url.indexOf('?') !== -1 ? '&' : '?';
                        hash = url.split('#');
                        url = hash[0] + separator + key + '=' + value;

                        if (hash[1]) {
                            url += '#' + hash[1];
                        }

                        return url;
                    } else {
                        return url;
                    }
                }
            },

            removeUrlParam: function removeURLParam(url, name) {
                var
                    re = new RegExp(name + '=[^&|#|$]*', 'gi');
                ;

                return url.replace(re, '');
            }
        };

    }
);