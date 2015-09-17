/**
 * Шаринг произвольной ссылки
 *
 */
window.ENTER.utils.shareLink = (function() {
    var
        vkontakte = function(purl, ptitle, pimg, text) {
            var url  = 'http://vkontakte.ru/share.php?';
            url += 'url='          + encodeURIComponent(purl);
            url += '&title='       + encodeURIComponent(ptitle);
            url += '&description=' + encodeURIComponent(text);
            url += '&image='       + encodeURIComponent(pimg);
            url += '&noparse=true';

            return url;
        },
        odnoklassniki = function(purl, text) {
            var url  = 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1';
            url += '&st.comments=' + encodeURIComponent(text);
            url += '&st._surl='    + encodeURIComponent(purl);

            return url;
        },
        facebook = function(purl, ptitle, pimg, text) {
            var url  = 'http://www.facebook.com/sharer.php?s=100';
            url += '&p[title]='     + encodeURIComponent(ptitle);
            url += '&p[summary]='   + encodeURIComponent(text);
            url += '&p[url]='       + encodeURIComponent(purl);
            url += '&p[images][0]=' + encodeURIComponent(pimg);

            return url;
        },
        twitter = function(purl, ptitle) {
            var url  = 'http://twitter.com/share?';
            url += 'text='      + encodeURIComponent(ptitle);
            url += '&url='      + encodeURIComponent(purl);
            url += '&counturl=' + encodeURIComponent(purl);

            return url;
        },
        mailru = function(purl, ptitle, pimg, text) {
            var url  = 'http://connect.mail.ru/share?';
            url += 'url='          + encodeURIComponent(purl);
            url += '&title='       + encodeURIComponent(ptitle);
            url += '&description=' + encodeURIComponent(text);
            url += '&imageurl='    + encodeURIComponent(pimg);

            return url;
        },
        googleplus = function(purl){
            var url = 'https://plus.google.com/share?url=';
            url +='{';
            url += encodeURIComponent(purl);
            url += '}';

            return url;
        },
        mail = function(purl, mail, title, body){
            var url = 'mailto:' + mail;
            url += '?subject=' + title;
            url += '&body=' + purl + "\n\r" + body;

            return url;
        }
    ;

    return {
        vkontakte : vkontakte,
        odnoklassniki: odnoklassniki,
        facebook : facebook,
        twitter : twitter,
        mailru : mailru,
        googleplus : googleplus,
        mail: mail
    }

})();
