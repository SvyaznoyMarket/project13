/**
 * Created by alexandr.anpilogov on 15.12.15.
 */
$(function(){
    var $section = $(".js-fixBtnWrap"),
        $el = $(".js-fixBtn"),
        $body = $(document.body);

/*    if($section.length) {
        $section.addClass('orderCnt_fix-btn');
    }*/

    if($(window).height() <= $section.height()){
        $el.addClass('fixed')
    }
});