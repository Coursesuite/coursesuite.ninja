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

/**
 * Note that this script is intended to be included at the *end* of the document, before </body>
 */
(function (window, document) {
if ('open' in document.createElement('details')) return;

// made global by myself to be reused elsewhere
var addEvent = (function () {
  if (document.addEventListener) {
    return function (el, type, fn) {
      if (el && el.nodeName || el === window) {
        el.addEventListener(type, fn, false);
      } else if (el && el.length) {
        for (var i = 0; i < el.length; i++) {
          addEvent(el[i], type, fn);
        }
      }
    };
  } else {
    return function (el, type, fn) {
      if (el && el.nodeName || el === window) {
        el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
      } else if (el && el.length) {
        for (var i = 0; i < el.length; i++) {
          addEvent(el[i], type, fn);
        }
      }
    };
  }
})();


/** details support - typically in it's own script */
// find the first /real/ node
function firstNode(source) {
  var node = null;
  if (source.firstChild.nodeName != "#text") {
    return source.firstChild;
  } else {
    source = source.firstChild;
    do {
      source = source.nextSibling;
    } while (source && source.nodeName == '#text');

    return source || null;
  }
}

function isSummary(el) {
  var nn = el.nodeName.toUpperCase();
  if (nn == 'DETAILS') {
    return false;
  } else if (nn == 'SUMMARY') {
    return true;
  } else {
    return isSummary(el.parentNode);
  }
}

function toggleDetails(event) {
  // more sigh - need to check the clicked object
  var keypress = event.type == 'keypress',
      target = event.target || event.srcElement;
  if (keypress || isSummary(target)) {
    if (keypress) {
      // if it's a keypress, make sure it was enter or space
      keypress = event.which || event.keyCode;
      if (keypress == 32 || keypress == 13) {
        // all's good, go ahead and toggle
      } else {
        return;
      }
    }

    var open = this.getAttribute('open');
    if (open === null) {
      this.setAttribute('open', 'open');
    } else {
      this.removeAttribute('open');
    }

    // this.className = open ? 'open' : ''; // Lame
    // trigger reflow (required in IE - sometimes in Safari too)
    setTimeout(function () {
      document.body.className = document.body.className;
    }, 13);

    if (keypress) {
      event.preventDefault && event.preventDefault();
      return false;
    }
  }
}

function addStyle() {
  var style = document.createElement('style'),
      head = document.getElementsByTagName('head')[0],
      key = style.innerText === undefined ? 'textContent' : 'innerText';

  var rules = ['details{display: block;}','details > *{display: none;}','details.open > *{display: block;}','details[open] > *{display: block;}','details > summary:first-child{display: block;cursor: pointer;}','details[open]{display: block;}'];
      i = rules.length;

  style[key] = rules.join("\n");
  head.insertBefore(style, head.firstChild);
}

var details = document.getElementsByTagName('details'),
    wrapper,
    i = details.length,
    j,
    first = null,
    label = document.createElement('summary');

label.appendChild(document.createTextNode('Details'));

while (i--) {
  first = firstNode(details[i]);

  if (first != null && first.nodeName.toUpperCase() == 'SUMMARY') {
    // we've found that there's a details label already
  } else {
    // first = label.cloneNode(true); // cloned nodes weren't picking up styles in IE - random
    first = document.createElement('summary');
    first.appendChild(document.createTextNode('Details'));
    if (details[i].firstChild) {
      details[i].insertBefore(first, details[i].firstChild);
    } else {
      details[i].appendChild(first);
    }
  }

  // this feels *really* nasty, but we can't target details :text in css :(
  j = details[i].childNodes.length;
  while (j--) {
    if (details[i].childNodes[j].nodeName === '#text' && (details[i].childNodes[j].nodeValue||'').replace(/\s/g, '').length) {
      wrapper = document.createElement('text');
      wrapper.appendChild(details[i].childNodes[j]);
      details[i].insertBefore(wrapper, details[i].childNodes[j]);
    }
  }

  first.legend = true;
  first.tabIndex = 0;
}

// trigger details in case this being used on it's own
document.createElement('details');
addEvent(details, 'click', toggleDetails);
addEvent(details, 'keypress', toggleDetails);
addStyle();

})(window, document);

// TODO: fix slideshow hack
var _currentSlide = 0;
function slideshow(index) {
	if (typeof index == "string") {
		switch (index) {
			case "precede":
				index = Math.max(_currentSlide-1, 0);
				break;
			case "advance":
				index = Math.min(_currentSlide+1, document.querySelectorAll("#slide_controls a").length-1);
				break;
		}
		console.log("current slide", _currentSlide, "index", index);
	}
    var n = (index * 459), // TODO: read thumbnail widths from config
          m = Math.max((index * 120) - 60, 0); // half a thumb on the left, plus a full thumb visible, no matter the index
    $("#current_slide").css({
	    "transform": "translateX(-" + n + "px)"
    });
    $("#slide_controls").css({
	    "transform": "translateX(-" + m + "px)"
    }).find("a").removeClass("active");
    $("a:eq(" + index + ")", "#slide_controls").addClass("active");
    // transition the background to match the content of the image/slide predominant colour (precalculated by thumbnailer)
    $("section.info > div.media > div.slide-wrapper > .viewport.current_slide").css("box-shadow","0 0 25px " + slides[index].bgcolor);
	_currentSlide = index;
}

$(function () {

	if (typeof flatpickr === "function") {
		flatpickr("input.flatpickr", {
			onOpen: function () {
				this.hasBeenShown = true;
			},
			onChange: function (d) {
				if (this.hasBeenShown) {
					document.location = document.location.origin + '/admin/showLog/date/'+escape(d.toISOString().slice(0,10));
				}
			}
		});
	}

	$("table.app-matrix").on("click", "a[href*='fastspring']", function (e) {
		e.preventDefault();
		fastspring.store.show({"url": this.getAttribute("href")});
	});

	$("body").on("click", "#fastspring-close", function (e) {
		e.preventDefault();
		fastspring.store.hide();
	});

    //
    if (typeof slides != 'undefined') {

        var $current_slide = $("#current_slide").html(""),
            $slide_nav = $("#slide_controls").html(""),
            _figure = function (obj) {
                if (obj.hasOwnProperty("video")) {
                    return "<iframe width='459' height='344' src='" + obj.video + "' frameborder='0' allowfullscreen class='slide' style='background-color:" + obj.bgcolor + ";'></iframe>";
                } else if (obj.hasOwnProperty("image")) {
                    return ["<figure class='slide' style='background-color:" + obj.bgcolor + ";'>",
                    	"<img src='" + obj.preview + "'>",
                    	"<a href='" + obj.image + "' target='_blank' title='Open in a new window'><i class='cs-camera'></i></a>",
                    	obj.caption ? "<figcaption>" + obj.caption + "</figcaption>" : "",
                    	"</figure>"].join("");
                }
                return obj.html;
            },
            _thumb = function (obj, index) {
	            return "<a href='javascript:slideshow(" + index + ");'" + (index==0 ? " class='active'" : "") + "><img src='" + obj.thumb + "'></a>";
            };
        slides.forEach(function(item,index) {
	        $current_slide.append(_figure(item));
	        $slide_nav.append(_thumb(item,index));
	    });

    }

    // colour cycle the scorm section background
    // why? why not!
    var $scormSection = $("section.section.scorm");
    if ($scormSection.length) {
        var _speed = 123,
            _cycle = function(H) {
                $scormSection.css("background-color", "hsla(" + H + ", 50%, 48%, 0.1)");
                setTimeout(_cycle, _speed, ((H + 1) % 360));
            }
        // _cycle(114);
    }

	$("a[data-action='hover-thumb']")
		.hover(function(e) {
			var img = $("<img>").attr({
				src: this.href,
				id: "hoverThumb"
			}).css({
				"position": "absolute",
				"top": (e.pageY+10) + "px", // fixed position then window.pageYOffset || document.documentElement.scrollTop
				"left": (e.pageX+10) + "px",
				// "max-width": "50%",
				"box-shadow": "0 0 25px rgba(0,0,0,.25)"
			});
			$("body").append(img);
		}, function () {
			$("#hoverThumb").remove();
		})
		.mousemove(function(e) {
			$("#hoverThumb").css({
				"top": (e.pageY+10) + "px",
				"left": (e.pageX+10) + "px",
			});
		});

});

		$("#hoverThumb").remove();
		})
		.mousemove(function(e) {
			$("#hoverThumb").css({
				"top": (e.pageY+10) + "px",
				"left": (e.pageX+10) + "px",
			});
		});

});

