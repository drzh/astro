function init(evt) {
    if (window.svgDocument == null) {
        svgDocument = evt.target.ownerDocument;
    }
    tooltip = svgDocument.getElementById('tooltip');
}

function showtooltip(evt, x, y, id) {
    mytt = document.getElementById(id)
    mytt.setAttributeNS(null, "style", "position:absolute;left:" + (x + 5) + "px;top:" + (y - 25) + "px;" + "background-color:#C0C0C0; color:black; padding-left:2px; padding-right:2px;");
    mytt.firstChild.data = evt.target.getAttributeNS(null, "name");
}

function hidetooltip(id) {
    mytt = document.getElementById(id)
    mytt.setAttributeNS(null, "style", "visibility:hidden");
    // tooltip.setAttributeNS(null,"visibility","hidden");
}
