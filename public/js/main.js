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
var _currentSlide = 0,
	_aATimeout, _autoAdvance = true;
function slideshow(index) {
	if (typeof index == "string") {
		switch (index) {
			case "precede":
				index = Math.max(_currentSlide-1, 0);
				break;
			case "advance":
				index = Math.min(_currentSlide+1, document.querySelectorAll("#slide_controls a").length-1);
				if (_currentSlide == index) index = 0;
				break;
		}
		//console.log("current slide", _currentSlide, "index", index);
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
    $("section.info > div.media > a.slide_navigation").css("color",slides[index].bgcolor);
	_currentSlide = index;
}

// make rich editors and other dooblie-doos
window.addEventListener("load", function () {

	document.querySelectorAll("form[method='ajax']").forEach(function (el, index) {
		$(el).on("submit", function (e) {
			e.preventDefault;
			var $this = $(this);
			$.post($this.attr("action"), $this.serialize(), function (result) {
				$("#contact-feedback").html("<p>Your message has been sent! Thanks for your interest.</p>");
				$(el).reset();
			});
			return false;
		});
	});

	document.querySelectorAll("textarea[data-markdown]").forEach(function (el,index) {
		el.simplemde = new SimpleMDE({
			element: el,
			spellChecker: false,
		});
	}),

	document.querySelectorAll("[data-sortable]").forEach(function (el, index) {
		Sortable.create(el, {
			handle: ".cs-air",
			onEnd: function (evt) {
				console.log(evt, evt.oldIndex, evt.newIndex);
				if (evt.from.getAttribute("data-table")) {
					$.post("/admin/editSections/0/order", {
						"table": evt.from.getAttribute("data-table"),
						"field": evt.from.getAttribute("data-field"),
						"order": function () {
							var ids = [];
							$(evt.item).parent().children().each(function(index,el) {
								ids[ids.length] = el.getAttribute("data-id");
							});
							return ids;
						}
					}, function (result) {
						console.log(result);
					});
				}
			},
			animation: 350
		});
	});

false});

function slideAdvance() {
	if (!_autoAdvance) return;
	slideshow("advance");
    _aATimeout = setTimeout(function(){slideAdvance()},4567);
}

$(function () {

	$("a[data-action='dismiss-message']").on("click", function(e) {
		var $this = $(this);
		$.getJSON("/message/done/" + this.getAttribute("data-action-id"), function (result) {
			if (result.updated) {
				console.log("updated", $this);
				$this.closest("div.acknowledgement-item").fadeOut(250, function() { console.log("faded", this); $(this).remove() });
			}
		});
	});

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
		flatpickr("input[type='datetime']");

	}

	/* $("table.app-matrix").on("click", "a[href*='fastspring']", function (e) {
		e.preventDefault();
		fastspring.store.show({"url": this.getAttribute("href")});
	}); */

	/* $("body").on("click", "#fastspring-close", function (e) {
		e.preventDefault();
		fastspring.store.hide();
	}); */

    //
    if (typeof slides != 'undefined') {

	    $("div.media").on("mouseenter mouseover", function () {
		    _autoAdvance = false;
		    if (_aATimeout) clearTimeout(_aATimeout);
	    });

        var $current_slide = $("#current_slide").html(""),
            $slide_nav = $("#slide_controls").html(""),
            _figure = function (obj) {
                if (obj.hasOwnProperty("video")) {
                    return "<iframe width='459' height='344' src='" + obj.video + "&enablejsapi=1' frameborder='0' allowfullscreen class='slide' style='background-color:" + obj.bgcolor + ";'></iframe>";
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


		$("section.info > div.media > a.slide_navigation").css("color",slides[0].bgcolor);
	    _aATimeout= setTimeout(function(){slideAdvance()},4567);

    }

    // colour cycle the scorm section background
    // why? why not!
    /*
    var $scormSection = $("section.section.scorm");
    if ($scormSection.length) {
        var _speed = 123,
            _cycle = function(H) {
                $scormSection.css("background-color", "hsla(" + H + ", 50%, 48%, 0.1)");
                setTimeout(_cycle, _speed, ((H + 1) % 360));
            }
        // _cycle(114);
    } */

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

