var width = window.innerWidth
    || document.documentElement.clientWidth
    || document.body.clientWidth;

var height = window.innerHeight
    || document.documentElement.clientHeight
    || document.body.clientHeight;

var color = d3.scale.category20();

// legend
$('.legend li').each(function() {
    var type = $(this).data('type');

    this.style.backgroundColor = color(type);
});

// end legend

