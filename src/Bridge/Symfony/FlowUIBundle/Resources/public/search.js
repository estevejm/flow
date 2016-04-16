(function ($, d3, flow) {

    function init(optArray) {
        optArray = optArray.sort();

        $("#search-form").show();

        $("#search-input").autocomplete({
            source: optArray
        });

        $(document).on('submit', '#search-form', function(e) {
            e.preventDefault();
            var selectedVal = document.getElementById('search-input').value;
            searchNode(selectedVal);
        });

        $(document).on('click', '.validation-item', function() {
            var nodeId = $(this).data('node-id');
            searchNode(nodeId);
        });
    }

    function searchNode(selectedVal) {
        //find the node
        var svg = d3.selectAll('.graph-container > svg');
        var node = svg.selectAll(".node");
        if (selectedVal == "none") {
            node.style("stroke", "white").style("stroke-width", "1");
        } else {
            var selected = node.filter(function (d, i) {
                return d.id != selectedVal;
            });
            selected.style("opacity", "0");
            var link = svg.selectAll(".link")
            link.style("opacity", "0");
            d3.selectAll(".node, .link").transition()
                .duration(2000)
                .style("opacity", 1);

            $('html, body').animate({
                scrollTop: $("#" + selectedVal).offset().top - (height/2)
            }, 500);
        }
    }

    flow.component.search = {
        init: init
    };

}(jQuery, d3, flow));