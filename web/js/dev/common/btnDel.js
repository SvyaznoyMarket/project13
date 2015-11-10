$(function(){
    var containerDel;
    $('.js-btnDelModal').on('click', function(e){
        e.preventDefault();

        var $this = $(this),
            content = $this.closest('.js-copyContentFrom');
            containerDel = $this.closest('.js-btnDelContainer');

        copyContent(content);
    });
    $('.js-btnContainerDel').on('click', function(){
        console.log(containerDel);
        containerDel.remove();
    });

    function copyContent(content){
        console.log(1);
        $('.js-copyContentIn').html(content.html());
    }
});
