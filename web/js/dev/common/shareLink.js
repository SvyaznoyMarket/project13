/**
 * Шаринг произвольной ссылки
 *
 * Пример использования:
 *
 * $body.on('click', '.personal-share__icon.fb',function(){

        shareLink.facebook('http://enter.ru','Ноутбук Lenovo A600', 'http://enter.ru/images/img1.jpg', 'Мой виш-лист')
    })

 */
shareLink = (function() {
    var
        vkontakte = function(purl, ptitle, pimg, text) {
            var url  = 'http://vkontakte.ru/share.php?';
            url += 'url='          + encodeURIComponent(purl);
            url += '&title='       + encodeURIComponent(ptitle);
            url += '&description=' + encodeURIComponent(text);
            url += '&image='       + encodeURIComponent(pimg);
            url += '&noparse=true';
            popup(url);
        },
        odnoklassniki = function(purl, text) {
            var url  = 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1';
            url += '&st.comments=' + encodeURIComponent(text);
            url += '&st._surl='    + encodeURIComponent(purl);
            popup(url);
        },
        facebook = function(purl, ptitle, pimg, text) {
            var url  = 'http://www.facebook.com/sharer.php?s=100';
            url += '&p[title]='     + encodeURIComponent(ptitle);
            url += '&p[summary]='   + encodeURIComponent(text);
            url += '&p[url]='       + encodeURIComponent(purl);
            url += '&p[images][0]=' + encodeURIComponent(pimg);
            popup(url);
        },
        twitter = function(purl, ptitle) {
            var url  = 'http://twitter.com/share?';
            url += 'text='      + encodeURIComponent(ptitle);
            url += '&url='      + encodeURIComponent(purl);
            url += '&counturl=' + encodeURIComponent(purl);
            popup(url);
        },
        mailru = function(purl, ptitle, pimg, text) {
            var url  = 'http://connect.mail.ru/share?';
            url += 'url='          + encodeURIComponent(purl);
            url += '&title='       + encodeURIComponent(ptitle);
            url += '&description=' + encodeURIComponent(text);
            url += '&imageurl='    + encodeURIComponent(pimg);
            popup(url)
        },
        googleplus = function(purl){
            var url = 'https://plus.google.com/share?url=';
            url +='{';
            url += encodeURIComponent(purl);
            url += '}';
            popup(url);
        },
        mailto = function(purl, mail, title, body){
            var url = 'mailto:' + mail;
            url += '?subject=' + title;
            url += '&body=' + purl + '<br>' + body;
            popup(url);// не уверена, что так сработает, может просто делать window.location.href = url ?
        },

        popup = function(url) {
            window.open(url,'','toolbar=0,status=0,width=626,height=436');
        };
    return {
        vkontakte : vkontakte,
        odnoklassniki: odnoklassniki,
        facebook : facebook,
        twitter : twitter,
        mailru : mailru,
        googleplus : googleplus,
        mailto: mailto
    }

})();
