/**
 * Created by alexandr.anpilogov on 15.12.15.
 */
$(function(){
    var $section = $(".js-fixBtnWrap"),
        $el = $(".js-fixBtn");

    if($(window).height() <= $section.height()){
        $el.addClass('fixed');
    }

    $(window).on('scroll', function(){
       if($(window).scrollTop() == ($(document).height() - $(window).height())){
           $el.removeClass('fixed');
       }else{
           $el.addClass('fixed');
       }
    });

    $(document).ready().trigger('scroll');
});