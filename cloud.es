<![CDATA[
    function init(evt) {
        if (window.svgDocument == null ) {
            svgDocument = evt.target.ownerDocument;
        }
        tooltip = svgDocument.getElementById('tooltip');
    }
]]>

