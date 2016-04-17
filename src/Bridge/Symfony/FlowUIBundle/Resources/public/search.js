(function ($, flow) {

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

        flow.component.graph.temporaryFadeAllExcept("#" + id);
    }

    function scrollToNode(node) {
        $('html, body').animate({
            scrollTop: $(node).offset().top - (height/2)
        }, 500);
    }

    flow.component.search = {
        init: init
    };

}(jQuery, flow));