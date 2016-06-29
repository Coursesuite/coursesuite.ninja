var fastspring = function() {
    /*
     * IE only, size the size is only used when needed
     */
    function windowSize() {
        var w = 0, h = 0;
        if(!(document.documentElement.clientWidth == 0)) {
            w = document.documentElement.clientWidth;
            h = document.documentElement.clientHeight;
        }
        else {
            w = document.body.clientWidth;
            h = document.body.clientHeight;
        }
        return {width:w,height:h};
    }
    function extend(object, extender) {
    	var o = {};
        for (var attribute in extender) { o[attribute] = extender[attribute] || object[attribute]; }
        return o;
    }
    var bo = {
        ie: window.ActiveXObject,
        ie6: window.ActiveXObject && (document.implementation != null) && (document.implementation.hasFeature != null) && (window.XMLHttpRequest == null),
        quirks: document.compatMode==='BackCompat'
    }
    return {
    	settings: { "url": "http://sites.fastspring.com/coursesuite/product/" },
        store: {
            show: function(options) {
                var opt = extend(fastspring.settings, options || {});

                var iframe = document.createElement('iframe'),
                	div = fastspring.store.div = document.createElement('div'),
					idiv = fastspring.store.idiv = document.createElement('div'),
					closer = fastspring.store.closer = document.createElement('a');

                closer.style.display = 'inline-block';
                closer.style.background = '#fff';
                closer.style.padding = '10px 20px 0';
                closer.style.fontSize = '18px';
                closer.style.lineHeight = '18px';
                closer.style.border = '1px solid #2c2c2c';
                closer.style.borderBottomWidth = '0';
                closer.innerHTML = 'Cancel purchase';
                closer.style.color = '#000';
                closer.style.textDecoration = 'none';
                closer.style.position = 'fixed';
                closer.setAttribute('id', 'fastspring-close');
                closer.setAttribute('href', '#');
                closer.style.top = '47px'; // top-offset + border-top-width + border-bottom-width
				closer.style.right = '53px'; // left-offset + border-left-width + border-right-width

                div.style.background = '#1B7FCC';
                div.style.opacity = 0.8;
                div.style.filter = 'alpha(opacity=80)';
                document.body.className += " no-scroll";

                if((bo.ie && bo.quirks) || bo.ie6) {
                    var size = windowSize();
                    div.style.position = 'absolute';
                    div.style.width = size.width + 'px';
                    div.style.height = size.height + 'px';
                    div.style.setExpression('top', "(t=document.documentElement.scrollTop||document.body.scrollTop)+'px'");
                    div.style.setExpression('left', "(l=document.documentElement.scrollLeft||document.body.scrollLeft)+'px'");
                } else {
                    div.style.width = '100%';
                    div.style.height = '100%';
                    div.style.top = '0';
                    div.style.left = '0';
                    div.style.position = 'fixed';
                }

                idiv.style.border = '1px solid #2c2c2c';
                if((bo.ie && bo.quirks) || bo.ie6) {
                    idiv.style.position = 'absolute';
                    idiv.style.setExpression('top', "75+((t=document.documentElement.scrollTop||document.body.scrollTop))+'px'");
                    idiv.style.setExpression('left', "55+((l=document.documentElement.scrollLeft||document.body.scrollLeft))+'px'");
                } else {
                    idiv.style.position = 'fixed';
                    idiv.style.top = '75px';
                    idiv.style.left = '55px';
                }

                div.style.zIndex = 99997; // lightbox
                idiv.style.zIndex = 99998; // iframe wrapper
                closer.style.zIndex = 99998; // close button

                document.body.appendChild(div);
                document.body.appendChild(idiv);
                document.body.appendChild(closer);

                iframe.style.width = (div.offsetWidth - 110) +'px';
                iframe.style.height = (div.offsetHeight - 150) +'px';
                iframe.style.border = 'none';
                iframe.style.backgroundColor = '#fff';
                iframe.style.display = 'block';
                iframe.frameBorder = 0;
                iframe.src = opt.url;

                idiv.appendChild(iframe);
            },
            hide: function(callback) {
                document.body.className = document.body.className.replace("no-scroll", "");
                if(fastspring.store.idiv && fastspring.store.div) {
                    document.body.removeChild(fastspring.store.idiv);
                    document.body.removeChild(fastspring.store.div);
                    document.body.removeChild(fastspring.store.closer);
                }
				if(callback) {
					eval(callback);
				}
            }
        },
        open: function(options) {
        	var opt = extend(fastspring.settings, options || {});
        	location.href = opt.url;
        }
    }
}();