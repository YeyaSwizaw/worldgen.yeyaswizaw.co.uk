
$(document).ready(function() {
    var map = L.map('map', { zoomControl: false }).setView([0, 0], 0);
    var layer = L.tileLayer('/test/apple/{x}/{y}', {
        continuousWorld: true,
        maxZoom: 0
    }).addTo(map);

    $('.menu').sidr({
        name: 'sidr-right',
        side: 'right',
        renaming: false,
        source: '.menu-content'
    });

    $('#show-menu').click(function() {
        $.sidr('open', 'sidr-right');
    });

    $(function() {
        var counter = 0;
        var isDragging = false;

        map.on("mousedown", function() {
            map.on("mousemove", function() {
                isDragging = true;
                map.off("mousemove");
            });
        });
        map.on("mouseup", function() {
            var wasDragging = isDragging;
            isDragging = false;
            map.off("mousemove");
            if(!wasDragging) {
                $.sidr('close', 'sidr-right');
            }
        });
    });

    // Menu Handlers
    $('.menu-form').submit(function(e) {
        e.preventDefault();
    });

    $('.menu-form').change(function(e) {
        if(e.target.name == 'seed') {
            layer.setUrl('/test/' + e.target.value + '/{x}/{y}');
        }
    });
});
