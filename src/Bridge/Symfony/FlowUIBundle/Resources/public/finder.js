(function (flow, $) {

    function find(nodeId) {
        var selectedNode = document.getElementById(nodeId);

        if (selectedNode !== null) {
            scrollToNode(selectedNode);
        }

        flow.component.graph.temporaryFadeAllExcept(nodeId);
    }

    function scrollToNode(node) {
        $('html, body').animate({
            scrollTop: $(node).offset().top - (flow.config.canvas.height/2)
        }, 500);
    }

    flow.component.finder = {
        find: find
    };

}(flow, jQuery));