$(function(){

    var $window = $(window);
    var scrollTime = 0.1;
    var scrollDistance = 70;

    $window.on("mousewheel DOMMouseScroll", function(event){

        event.preventDefault();

        var delta = event.originalEvent.wheelDelta/120 || -event.originalEvent.detail/3;
        var scrollTop = $window.scrollTop();
        var finalScroll = scrollTop - parseInt(delta*scrollDistance);

        TweenMax.to($window, scrollTime, {
            scrollTo : { y: finalScroll, autoKill:true },
            ease: Power1.easeOut,
            overwrite: 5
        });

    });
});





$(document).ready(function() {
    $('tip').tooltip();
});

$(function(){
    $("[data-tooltip]").tooltip({ html: true });
    $(".alert").alert();
    $("[data-confirm]").on('click', function(){
        return confirm($(this).data('confirm'));
    });
});

$(document).ready(function(){
    $('.status').click(function() { $('.arrow').css("left", 0);});
    $('.photos').click(function() { $('.arrow').css("left", 80);});
    $('.videos').click(function() { $('.arrow').css("left", 160);});
});