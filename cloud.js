function init(evt) {
    if (window.svgDocument == null) {
        svgDocument = evt.target.ownerDocument;
    }
}

function getEventClientPoint(evt) {
    if (!evt) {
        return null;
    }

    if (typeof evt.clientX === 'number' && typeof evt.clientY === 'number') {
        return { clientX: evt.clientX, clientY: evt.clientY };
    }

    if (evt.touches && evt.touches.length > 0) {
        return { clientX: evt.touches[0].clientX, clientY: evt.touches[0].clientY };
    }

    if (evt.changedTouches && evt.changedTouches.length > 0) {
        return { clientX: evt.changedTouches[0].clientX, clientY: evt.changedTouches[0].clientY };
    }

    return null;
}

function getTooltipMetrics(element, tooltip) {
    var stage = element ? element.closest('.responsive-stage') : null;
    var frame = element ? element.closest('.responsive-stage-frame') : null;
    var container = frame || (tooltip ? tooltip.offsetParent || tooltip.parentElement : null);
    if (!container) {
        return null;
    }

    var containerRect = container.getBoundingClientRect();
    var width = container.clientWidth || containerRect.width || 0;
    var height = container.clientHeight || containerRect.height || 0;
    var scaleX = 1;
    var scaleY = 1;
    if (frame && stage) {
        var stageWidth = stage.offsetWidth || Number(stage.dataset.stageWidth) || width || 0;
        var stageHeight = stage.offsetHeight || Number(stage.dataset.stageHeight) || height || 0;
        scaleX = stageWidth ? width / stageWidth : 1;
        scaleY = stageHeight ? height / stageHeight : 1;
    }

    return {
        rect: containerRect,
        width: width,
        height: height,
        scaleX: scaleX || 1,
        scaleY: scaleY || 1
    };
}

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function showtooltip(evt, x, y, id) {
    var mytt = document.getElementById(id);
    if (!mytt) {
        return;
    }

    var source = evt && evt.target ? evt.target : null;
    var metrics = getTooltipMetrics(source || mytt, mytt);
    var point = getEventClientPoint(evt);
    var left = x + 8;
    var top = y - 28;
    if (metrics && point) {
        left = point.clientX - metrics.rect.left + 12;
        top = point.clientY - metrics.rect.top - 28;
    } else if (metrics) {
        left = x * metrics.scaleX + 8;
        top = y * metrics.scaleY - 28;
    }

    mytt.textContent = source ? (source.getAttribute('data-tooltip') || source.getAttribute('name') || '') : '';
    if (metrics && metrics.width && metrics.height) {
        var padding = 8;
        var tooltipWidth = mytt.offsetWidth || 0;
        var tooltipHeight = mytt.offsetHeight || 0;
        left = clamp(left, padding, Math.max(padding, metrics.width - tooltipWidth - padding));
        top = clamp(top, padding, Math.max(padding, metrics.height - tooltipHeight - padding));
    }

    mytt.style.left = left + 'px';
    mytt.style.top = top + 'px';
    mytt.style.visibility = 'visible';
}

function hidetooltip(id) {
    var mytt = document.getElementById(id);
    if (!mytt) {
        return;
    }

    mytt.style.visibility = 'hidden';
}
