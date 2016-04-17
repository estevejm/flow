(function (flow, $) {

    function init(graphs) {

        $("#search-form").show();

        $("#search-input").autocomplete({
            source: getNodeIds(graphs)
        });

        $(document).on('submit', '#search-form', function(e) {
            e.preventDefault();
            var inputValue = document.getElementById('search-input').value;
            flow.component.finder.find(inputValue);
        });
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

    flow.component.search = {
        init: init
    };

}(flow, jQuery));