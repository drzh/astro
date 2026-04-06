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

    var container = mytt.offsetParent || mytt.parentElement;
    var containerRect = container ? container.getBoundingClientRect() : { left: 0, top: 0 };
    var left = (evt && typeof evt.clientX === 'number') ? (evt.clientX - containerRect.left + 12) : (x + 8);
    var top = (evt && typeof evt.clientY === 'number') ? (evt.clientY - containerRect.top - 28) : (y - 28);

    mytt.style.visibility = 'visible';
    mytt.style.left = left + 'px';
    mytt.style.top = top + 'px';
    mytt.textContent = evt.target.getAttributeNS(null, 'data-tooltip') || evt.target.getAttributeNS(null, 'name') || '';
}

function hidetooltip(id) {
    var mytt = document.getElementById(id);
    if (!mytt) {
        return;
    }

    mytt.style.visibility = 'hidden';
}
