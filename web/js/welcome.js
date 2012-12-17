$(document).ready(function () {
	/* admitad */
	if( document.location.search.match(/admitad_uid/) ) {
		var url_s = parse_url( document.location.search )
		docCookies.setItem( false, "admitad_uid", url_s.admitad_uid, 31536e3, '/') // 31536e3 == one year
	}

	/* sclub card number */
	if( document.location.search.match(/scid/) ) {
		var url_s = parse_url( document.location.search )
		docCookies.setItem( false, "scId", url_s.scid, 31536e3, '/') // 31536e3 == one year
	}
    
    var shortinfo = '/user/shortinfo'
    if( !docCookies.hasItem('enter') ||  !docCookies.hasItem('enter_auth'))
        shortinfo += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000)
    if( $('#topBasket') ) {
        $.getJSON( shortinfo, function(data) {
            if( data.success ) {
                if (data.data.vitems>0) {
                    $('#topBasket').text( '('+data.data.vitems+')' )
                }
                if( data.data.name ) {
                    var dtmpl={}
                    dtmpl.user = data.data.name
                    var show_user = tmpl('auth_tmpl', dtmpl)
                    $('#auth-link').hide()
                    $('#auth-link').after(show_user)
                } else $('#auth-link').show()
            }
        })
    }

    /* Search */
    $('.startse').bind({
        'blur':function () {
            if (this.value == '')
                this.value = 'Поиск среди 30 000 товаров'
        },
        'focus':function () {
            if (this.value == 'Поиск среди 30 000 товаров')
                this.value = ''
        }
    })
    if (!$('#main_banner-data').length)
        return
    var promos = $('#main_banner-data').data('value')

    /* Shit happens */
    for (var i = 0; i < promos.length; i++) {
        if (typeof(promos[i].imgb) === 'undefined' || typeof(promos[i].imgs) === 'undefined') {
            promos.splice(i, 1)
        }
        if (typeof(promos[i].url) === 'undefined') {
            promos[i].url = ''
        }
        if (typeof(promos[i].t) === 'undefined') {
            promos[i].url = 4000
        }
        if (typeof(promos[i].alt) === 'undefined') {
            promos[i].url = ''
        }
    }
    var l = promos.length
    if (l == 0)
        return
    if (l == 1) {
        if ('is_exclusive' in promos[0] && promos[0].is_exclusive) {
            var exclImg = $('<img>').attr('src', promos[0].imgb).css('cursor', 'pointer').data('url', promos[0].url)
                .click(function () {
                    if (typeof(_gaq) !== 'undefined' && typeof(promos[0].ga) !== 'undefined')
                        _gaq.push(['_trackEvent', 'BannerClick', promos[0].ga ]);
                    location.href = $(this).data('url')
                })
            $('.bCarouselWrap').html(exclImg)
            return
        }
        $('.centerImage').attr('src', promos[0].imgb).data('url', promos[0].url)
            .click(function () {
                if (typeof(_gaq) !== 'undefined' && typeof(promos[0].ga) !== 'undefined')
                    _gaq.push(['_trackEvent', 'BannerClick', promos[0].ga ]);
                location.href = $(this).data('url')
            })
        return
    }
    /* Preload */
    var hb = $('<div>').css('display', 'none')
    for (var i = 0; i < l; i++) {
        $('<img>').attr('src', promos[i].imgb).appendTo(hb)
        $('<img>').attr('src', promos[i].imgs).appendTo(hb)
    }
    $('body').append(hb)

    /* Init */
    $('.leftImage').attr({ "src":promos[l - 1].imgs, "alt":promos[l - 1].alt, "title":promos[l - 1].alt})
    $('.centerImage').attr('src', promos[0].imgb).data('url', promos[0].url)
    $('.rightImage').attr({ "src":promos[1].imgs, "alt":promos[1].alt, "title":promos[1].alt})
    var currentSl = promos.length - 1
    var idto = null
    var initis = []
    var sliding = false
    var permission = true
    changeSrc(currentSl)
    idto = setTimeout(function () {
        goSlide()
    }, initis[1].t)
    /* Visuals */
    $("html").css('overflow-x', 'hidden')
	
	var b = new brwsr()
    if ( b.isAndroid || b.isOSX) {
        $('.bCarousel div').show()
        $('.allpage').css('overflow', 'hidden')
    } else {
        $('.bCarousel').mouseenter(
            function () {
                $('.bCarousel div').show()
            }).mouseleave(function () {
                $('.bCarousel div').hide()
            })
    }
    $('.leftArrow').click(function () {
        goSlide(-1)
    })
    $('.leftImage').click(function () {
        goSlide(-1)
    })
    $('.rightArrow').click(function () {
        goSlide(1)
    })
    $('.rightImage').click(function () {
        goSlide(1)
    })
    $('.centerImage').click(function () {
        clearTimeout(idto)
        if (typeof(_gaq) !== 'undefined' && typeof(initis[1].ga) !== 'undefined')
            _gaq.push(['_trackEvent', 'BannerClick', initis[1].ga ]);
        location.href = $(this).data('url')
    })
    $('.promos').click(function () {
        location.href = $(this).data('url')
    })
    $('.centerImage').hover(function () {
        permission = false
    }, function () {
        permission = true
    })

    function sideBanner(block, i) {
        $(block).animate({
                "opacity":"0"
            },
            400,
            function () {
                setTimeout(function () {
                    block.attr({ "src":initis[i].imgs, "alt":initis[i].alt, "title":initis[i].alt})
                    $(block).animate({
                        "opacity":"1"
                    })
                }, 350)
            })
    }

    function changeSrc(currentSl) {
        var delta = promos.length - currentSl - 3
        if (delta >= 0)
            initis = promos.slice(currentSl, currentSl + 3)
        else
            initis = promos.slice(currentSl).concat(promos.slice(0, -delta))
    }

    function goSlide(dir) {
        if (!permission) {
            idto = setTimeout(function () {
                goSlide()
            }, initis[1].t)
            return
        }
        if (sliding)
            return false
        sliding = true
        if (!dir)
            var dir = 1
        else // custom call
            clearTimeout(idto)
        var shift = '-=1000px',
            inileft = '1032px'

        if (dir < 0) {
            shift = '+=1000px',
                inileft = '-968px'
        }
        currentSl = (currentSl + dir) % promos.length
        if (currentSl < 0)
            currentSl = promos.length - 1
        changeSrc(currentSl)

        $('.centerImage').animate(
            {
                'left':shift,
                'opacity':'0'
            },
            800,
            function () {
                $('.centerImage').attr("src", initis[1].imgb).data('url', initis[1].url)
                $(this).css('left', inileft).animate({
                    'opacity':'1',
                    'left':shift
                })
                sliding = false
                idto = setTimeout(function () {
                    goSlide()
                }, initis[1].t) // AUTOPLAY
            })
        sideBanner($('.leftImage'), 0)
        sideBanner($('.rightImage'), 2)
    }
})