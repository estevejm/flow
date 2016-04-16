(function ($, d3, flow) {

    function init(nodeIds) {
        nodeIds = nodeIds.sort();

        $("#search-form").show();

        $("#search-input").autocomplete({
            source: nodeIds
        });

        setUpEvents();
    }

    function setUpEvents() {
        $(document).on('submit', '#search-form', function(e) {
            e.preventDefault();
            var inputValue = document.getElementById('search-input').value;
            searchNode(inputValue);
        });

        $(document).on('click', '.validation-item', function() {
            var nodeId = $(this).data('node-id');
            searchNode(nodeId);
        });
    }

    function searchNode(id) {
        var selectedNode = document.getElementById(id);

        if (selectedNode !== null) {
            scrollToNode(selectedNode);
        }

        temporaryFadeAllExcept("#" + id);
    }

    function temporaryFadeAllExcept(exceptionSelector) {
        var svg = d3.selectAll('.graph-container > svg');
        var nodes = svg.selectAll(".node:not(" + exceptionSelector + ")");
        var links = svg.selectAll(".link");

        nodes.style("opacity", "0");
        links.style("opacity", "0");

        d3.selectAll(".node, .link")
            .transition()
            .duration(2000)
            .style("opacity", 1);
    }

    function scrollToNode(node) {
        $('html, body').animate({
            scrollTop: $(node).offset().top - (height/2)
        }, 500);
    }

    flow.component.search = {
        init: init
    };

}(jQuery, d3, flow));