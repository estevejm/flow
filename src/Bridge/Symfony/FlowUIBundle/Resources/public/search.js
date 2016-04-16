(function ($, d3, flow) {

    function init(graphs) {

        $("#search-form").show();

        $("#search-input").autocomplete({
            source: getNodeIds(graphs)
        });

        setUpEvents();
    }

    function getNodeIds(graphs) {
        var nodeIds = [];

        for (id in graphs) {
            if (graphs.hasOwnProperty(id)) {
                var graph = graphs[id];

                for (var i = 0; i < graph.nodes.length; i++) {
                    nodeIds.push(graph.nodes[i].id);
                }

            }
        }

        return nodeIds.sort();
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
        var nodes = d3.selectAll(".node:not(" + exceptionSelector + ")");
        var links = d3.selectAll(".link");

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