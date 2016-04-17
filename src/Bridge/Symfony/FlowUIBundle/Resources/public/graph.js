(function (flow, $, d3) {

    var color = d3.scale.category20();

    $('.legend li').each(function() {
        var type = $(this).data('type');

        this.style.backgroundColor = color(type);
    });

    d3.json(flow.config.url.graph, function(error, graphs) {

        if (error) throw "Error rendering graph: " + error;

        createGraphs(graphs);

        if (typeof flow.component.search !== 'undefined') {
            flow.component.search.init(graphs);
        }
    });

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

        var links = container.selectAll(".link").data(graph.links);
        var nodes = container.selectAll(".node").data(graph.nodes);

        createForceLayout(graph, nodes, links);
        setupNodeNeighborhood(graph, nodes, links);
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

    function createForceLayout(graph, nodes, links) {
        var force = d3.layout.force()
            .charge(flow.config.force.charge)
            .linkDistance(flow.config.force.linkDistance)
            .size([flow.config.canvas.width, flow.config.canvas.height]);

        force
            .nodes(graph.nodes)
            .links(graph.links)
            .start();

        links.enter().append("line")
            .attr("class", "link")
            .attr("marker-end", "url(#end)");

        nodes.enter().append("circle")
            .attr("class", "node")
            .attr("id", function (d) { return d.id; })
            .attr("r", 8)
            .style("fill", function (d) { return color(d.type); })
            .call(force.drag);

        nodes.enter().append("text")
            .attr("dx", 10)
            .attr("dy", "0.35em")
            .attr("data-for-node", function (d) { return d.id; })
            .text(function(d) { return d.id });

        force.on("tick", function() {
            links.attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });

            d3.selectAll("circle")
                .attr("cx", function (d) { return d.x; })
                .attr("cy", function (d) { return d.y; });

            d3.selectAll("text")
                .attr("x", function (d) { return d.x; })
                .attr("y", function (d) { return d.y; });
        });
    }

    function setupNodeNeighborhood(graph, nodes, links) {
        var neighborhood = new Neighborhood(graph);

        var toggle = 0;

        nodes.on('dblclick', function connectedNodes() {
            if (toggle == 0) {
                showConnected(this, neighborhood, nodes, links);
                toggle = 1;
            } else {
                showAll(nodes, links);
                toggle = 0;
            }
        });
    }
    function Neighborhood(graph) {
        var self = this;
        this.linkedNodes = {};

        for (var i = 0; i < graph.nodes.length; i++) {
            this.linkedNodes[i + "," + i] = 1;
        }

        graph.links.forEach(function (link) {
            self.linkedNodes[link.source.index + "," + link.target.index] = 1;
        });
    }

    Neighborhood.prototype.areNeighbors  = function(node1, node2) {
        return this.linkedNodes[node1.index + "," + node2.index];
    };

    function showConnected(selectedNode, neighborhood, nodes, links) {
        var d = d3.select(selectedNode).node().__data__;
        nodes.style("opacity", function (o) {
            return neighborhood.areNeighbors(d, o) || neighborhood.areNeighbors(o, d) ? 1 : 0.1;
        });

        links.style("opacity", function (o) {
            return d.index == o.source.index || d.index == o.target.index ? 1 : 0.1;
        });
    }

    function showAll(nodes, links) {
        nodes.style("opacity", 1);
        links.style("opacity", 1);
    }

    function temporaryFadeAllExcept(nodeId) {
        var nodes = d3.selectAll(".node:not(#" + nodeId + ")");
        var texts = d3.selectAll("text:not([data-for-node='" + nodeId + "'])");
        var links = d3.selectAll(".link");

        nodes.style("opacity", "0");
        texts.style("opacity", "0");
        links.style("opacity", "0");

        d3.selectAll(".node, .link, text")
            .transition()
            .duration(2000)
            .style("opacity", 1);
    }

    flow.component.graph = {
        temporaryFadeAllExcept: temporaryFadeAllExcept
    };

}(flow, jQuery, d3));