function init(evt) {
    if (window.svgDocument == null) {
        svgDocument = evt.target.ownerDocument;
    }
}

function showtooltip(evt, x, y, id) {
    var mytt = document.getElementById(id);
    if (!mytt) {
        return;
    }

    mytt.style.visibility = 'visible';
    mytt.style.left = (x + 8) + 'px';
    mytt.style.top = (y - 28) + 'px';
    mytt.textContent = evt.target.getAttributeNS(null, 'data-tooltip') || evt.target.getAttributeNS(null, 'name') || '';
}

function hidetooltip(id) {
    var mytt = document.getElementById(id);
    if (!mytt) {
        return;
    }

    mytt.style.visibility = 'hidden';
}
