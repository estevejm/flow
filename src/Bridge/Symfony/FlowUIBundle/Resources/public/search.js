(function ($, PubSub) {

    PubSub.subscribe('graph.loaded', function (msg, graphs) {
        $("#search-form").show();

        $("#search-input").autocomplete({
            source: getNodeIds(graphs)
        });

        $(document).on('submit', '#search-form', function(e) {
            e.preventDefault();
            var inputValue = document.getElementById('search-input').value;
            // todo: validate input value
            PubSub.publish('node.find', inputValue);
        });
    });

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

}(jQuery, PubSub));