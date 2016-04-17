(function (flow, $, d3) {

    var color = d3.scale.category20();

    function load(uri) {
        d3.json(uri, function(error, graphs) {

            if (error) throw "Error rendering graph: " + error;

            PubSub.publish('graph.loaded', graphs);

            createGraphs(graphs);
        });

        $('.legend li').each(function() {
            var type = $(this).data('type');

            this.style.backgroundColor = color(type);
        });
    }

    function createGraphs(graphs) {
        for (id in graphs) {
            if (graphs.hasOwnProperty(id)) {
                var graph = graphs[id];
                createGraph(id, graph);
            }
        }
    }

    function createGraph(id, graph) {
        var container = createGraphContainer(id);

        var force = d3.layout.force()
            .charge(flow.config.force.charge)
            .linkDistance(flow.config.force.linkDistance)
            .size([flow.config.canvas.width, flow.config.canvas.height])
            .nodes(graph.nodes)
            .links(graph.links)
            .start();

        var links = container.selectAll(".link")
            .data(graph.links)
            .enter().append("line")
            .attr("class", "link")
            .attr("marker-end", "url(#end)");

        var nodes = container.selectAll(".node")
            .data(graph.nodes);

        var circles = nodes.enter().append("circle")
            .attr("class", "node")
            .attr("id", function (d) { return d.id; })
            .attr("r", 8)
            .style("fill", function (d) { return color(d.type); })

        circles.call(force.drag);

        var texts = nodes.enter().append("text")
            .attr("dx", 10)
            .attr("dy", "0.35em")
            .attr("class", "label")
            .attr("data-for-node", function (d) { return d.id; })
            .text(function(d) { return d.id });

        force.on("tick", function() {
            links
                .attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });

            circles
                .attr("cx", function (d) { return d.x; })
                .attr("cy", function (d) { return d.y; });

            texts
                .attr("x", function (d) { return d.x; })
                .attr("y", function (d) { return d.y; });
        });
    }

    function createGraphContainer(id) {
        var container = d3.select("body")
            .append("div")
            .attr("id",  "#graph-" + id)
            .attr("class", "graph-container well")
            .append("svg")
            .attr("viewBox", "0 0 " + flow.config.canvas.width + " " + flow.config.canvas.height )
            .attr("preserveAspectRatio", "xMidYMid meet");

        // arrow
        container.append("svg:defs").selectAll("marker")
            .data(["end"])
            .enter().append("marker")
            .attr("id", String)
            .attr("viewBox", "0 -5 12 12")
            .attr("refX", 25)
            .attr("refY", -1.5)
            .attr("markerWidth", 7)
            .attr("markerHeight", 7)
            .attr("orient", "auto")
            .append("path")
            .attr("d", "M0,-5L10,0L0,5")
            .style("stroke", "#4679BD")
            .style("opacity", "0.6");

        return container;
    }

    flow.component.graph = {
        load: load
    }

}(flow, jQuery, d3));