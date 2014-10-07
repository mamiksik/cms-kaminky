/*$(function(){

    var $window = $(window);
    var scrollTime = 0.1;
    var scrollDistance = 270;

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
});*/



/*

$(function(){
    $("[data-tooltip]").tooltip({ html: true });
    $(".alert").alert();
    $("[data-confirm]").on('click', function(){
        return confirm($(this).data('confirm'));
    });
});*/

$('a').tooltip();
$('button').tooltip();
/*$('.tool2').tooltip();

$("* [rel='tooltip']").tooltip({
    html: true,
    placement: 'bottom'
});*/

$(document).ready(function(){
    $('.status').click(function() { $('.arrow').css("left", 0);});
    $('.photos').click(function() { $('.arrow').css("left", 80);});
    $('.videos').click(function() { $('.arrow').css("left", 160);});
});


var myCenter = new google.maps.LatLng(49.177517, 16.558834);

function initialize()
{
    var mapOpt = {
        center: myCenter,
        zoom: 18,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false
    };
    var map=new google.maps.Map(document.getElementById("googleMap"),mapOpt);

    var marker = new google.maps.Marker({
        position: myCenter,
        title:'Click to zoom'
    });

    marker.setMap(map);

    google.maps.event.addListener(marker,'click',function() {
        map.setZoom(18);
        map.setCenter(marker.getPosition());
    });

    google.maps.event.addListener(map,'center_changed',function() {
        // 3 seconds after the center of the map has changed, pan back to the marker
        window.setTimeout(function() {
            map.panTo(marker.getPosition());
        },9000);
    });


}

google.maps.event.addDomListener(window, 'load', initialize);


