
// feature detection
document.querySelector("html").setAttribute("class", ('ontouchstart' in window ? "" : "no-") + "touch");

// make <details> available
(function(k,c){function p(a){if("#text"!=a.firstChild.nodeName)return a.firstChild;a=a.firstChild;do a=a.nextSibling;while(a&&"#text"==a.nodeName);return a||null}function m(a){var d=a.nodeName.toUpperCase();return"DETAILS"==d?!1:"SUMMARY"==d?!0:m(a.parentNode)}function n(a){var d="keypress"==a.type,b=a.target||a.srcElement;if(d||m(b)){if(d&&(d=a.which||a.keyCode,32!=d&&13!=d))return;null===this.getAttribute("open")?this.setAttribute("open","open"):this.removeAttribute("open");setTimeout(function(){c.body.className=
c.body.className},13);if(d)return a.preventDefault&&a.preventDefault(),!1}}if(!("open"in c.createElement("details"))){var h=function(){return c.addEventListener?function(a,d,b){if(a&&a.nodeName||a===k)a.addEventListener(d,b,!1);else if(a&&a.length)for(var c=0;c<a.length;c++)h(a[c],d,b)}:function(a,b,c){if(a&&a.nodeName||a===k)a.attachEvent("on"+b,function(){return c.call(a,k.event)});else if(a&&a.length)for(var d=0;d<a.length;d++)h(a[d],b,c)}}(),b=c.getElementsByTagName("details"),l,e=b.length,g,
f=null;for(c.createElement("summary").appendChild(c.createTextNode("Details"));e--;){f=p(b[e]);if(null==f||"SUMMARY"!=f.nodeName.toUpperCase())f=c.createElement("summary"),f.appendChild(c.createTextNode("Details")),b[e].firstChild?b[e].insertBefore(f,b[e].firstChild):b[e].appendChild(f);for(g=b[e].childNodes.length;g--;)"#text"===b[e].childNodes[g].nodeName&&(b[e].childNodes[g].nodeValue||"").replace(/\s/g,"").length&&(l=c.createElement("text"),l.appendChild(b[e].childNodes[g]),b[e].insertBefore(l,
b[e].childNodes[g]));f.legend=!0;f.tabIndex=0}c.createElement("details");h(b,"click",n);h(b,"keypress",n);(function(){var a=c.createElement("style"),b=c.getElementsByTagName("head")[0],f=void 0===a.innerText?"textContent":"innerText",g="details{display: block;},details > *{display: none;},details.open > *{display: block;},details[open] > *{display: block;},details > summary:first-child{display: block;cursor: pointer;},details[open]{display: block;}".split(",");e=g.length;a[f]=g.join("\n");b.insertBefore(a,
b.firstChild)})()}})(window,document);

// Copies a string to the clipboard. Must be called from within an
// event handler such as click. May return false if it failed, but
// this is not always possible. Browser support for Chrome 43+,
// Firefox 42+, Safari 10+, Edge and IE 10+.
// IE: The clipboard feature may be disabled by an administrator. By
// default a prompt is shown the first time the clipboard is
// used (per session).
function copyToClipboard(element) {
    var text = element.textContent;
    if (window.clipboardData && window.clipboardData.setData) {
        // IE specific code path to prevent textarea being shown while dialog is visible.
        return clipboardData.setData("Text", text);

    } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
        document.body.appendChild(textarea);
        textarea.select();
        var r = false;
        try {
            r = document.execCommand("copy");  // Security exception may be thrown by some browsers.
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
        } finally {
            document.body.removeChild(textarea);
        }
        if (r) {
        	var label = element.dataset.label;
        	element.dataset.label = "Copied!";
        	setTimeout(function() {
        		element.dataset.label = label;
        	},2000);
        }
        return r;
    }
}

/* fastspring contextual store callback */
function fsPopupClosed(evnt) {
	if (evnt) {
		fastspring.builder.reset();
		var feedback = UIkit.modal.dialog('<div class="uk-modal-body uk-text-center"><div uk-spinner class="uk-margin-right"></div>Validating purchase information, please wait a moment ...</div>', {
			escClose: false,
        	bgClose: false,
        	overlay: true,
        	clsPanel: 'uk-modal-dialog uk-margin-auto-vertical'
		});
		new Promise(function(resolve,reject) {
			function poll() {
				fetch("/api/validateorder/" + Text_base64enc(evnt), {method:'GET',headers:{'content-type':'application/json','X-Requested-With':'XMLHttpRequest'},cache:'no-cache',credentials:'omit'})
				.then(function(response) {
					return response.json()
				})
				.then(function(data) {
					if (data.ready===true) {
						resolve(data);
					} else {
						setTimeout(poll,1002);
					}
				});
			}
			poll();
		}).then(function(data) {
			feedback.hide();
			location.href = "/login/callback/" + Text_base64enc(evnt);
		});
	}
}

function fsPrepopulate (email, token) {
  fastspring.builder.recognize({
    "email" : email
  });
  fastspring.builder.tag('token',token);
}

function str_replace (search, replace, subject, countObj) {
  //  discuss at: http://locutus.io/php/str_replace/
  // original by: Kevin van Zonneveld (http://kvz.io)
  // improved by: Gabriel Paderni
  // improved by: Philip Peterson
  // improved by: Simon Willison (http://simonwillison.net)
  // improved by: Kevin van Zonneveld (http://kvz.io)
  // improved by: Onno Marsman (https://twitter.com/onnomarsman)
  // improved by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // bugfixed by: Anton Ongson
  // bugfixed by: Kevin van Zonneveld (http://kvz.io)
  // bugfixed by: Oleg Eremeev
  // bugfixed by: Glen Arason (http://CanadianDomainRegistry.ca)
  // bugfixed by: Glen Arason (http://CanadianDomainRegistry.ca)
  //    input by: Onno Marsman (https://twitter.com/onnomarsman)
  //    input by: Brett Zamir (http://brett-zamir.me)
  //    input by: Oleg Eremeev
  //      note 1: The countObj parameter (optional) if used must be passed in as a
  //      note 1: object. The count will then be written by reference into it's `value` property
  //   example 1: str_replace(' ', '.', 'Kevin van Zonneveld')
  //   returns 1: 'Kevin.van.Zonneveld'
  //   example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars')
  //   returns 2: 'hemmo, mars'
  //   example 3: str_replace(Array('S','F'),'x','ASDFASDF')
  //   returns 3: 'AxDxAxDx'
  //   example 4: var countObj = {}
  //   example 4: str_replace(['A','D'], ['x','y'] , 'ASDFASDF' , countObj)
  //   example 4: var $result = countObj.value
  //   returns 4: 4

  var i = 0
  var j = 0
  var temp = ''
  var repl = ''
  var sl = 0
  var fl = 0
  var f = [].concat(search)
  var r = [].concat(replace)
  var s = subject
  var ra = Object.prototype.toString.call(r) === '[object Array]'
  var sa = Object.prototype.toString.call(s) === '[object Array]'
  s = [].concat(s)

  if (typeof (search) === 'object' && typeof (replace) === 'string') {
    temp = replace
    replace = []
    for (i = 0; i < search.length; i += 1) {
      replace[i] = temp
    }
    temp = ''
    r = [].concat(replace)
    ra = Object.prototype.toString.call(r) === '[object Array]'
  }

  if (typeof countObj !== 'undefined') {
    countObj.value = 0
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + ''
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0]
      s[i] = (temp).split(f[j]).join(repl)
      if (typeof countObj !== 'undefined') {
        countObj.value += ((temp.split(f[j])).length - 1)
      }
    }
  }
  return sa ? s : s[0]
}

/* js versions of Text::base64enc */
function Text_base64enc(obj) {
	return str_replace(['+','/','='], ['-','_',','], btoa(typeof obj === "string" ? obj : JSON.stringify(obj)));
}
function Text_base64dec(str) {
	return str_replace(['-','_',','], ['+','/','='], atob(str));
}

// // https://stackoverflow.com/a/45086170/1238884
// // const disposerFn = () => { scope.removeEventListener(type, handler, capture); } disposerFn.type = type; return disposerFn;
// // const ownAddEventListener = (scope, type, handler, capture) => {
// //   scope.addEventListener(type, handler, capture);
// //   return () => {
// //     scope.removeEventListener(type, handler, capture);
// //   }
// // }
// // const disposer = ownAddEventListener(document.body, 'scroll', () => {
//   // do something
// // }, false);

// // disposer(); -> calls removeEventListener

// var ajaxSubmit = function (frm) {

// 	var $this = $(frm);
// 	$feedback = $("#form-feedback", $this).html("").removeAttr("class");
// 	$.post($this.attr("action"), $this.serialize(), function (result) {

// 		if (result.csrf && $("input[name='csrf_token']", $this).length) {
// 			$("input[name='csrf_token']", $this).val(result.csrf);
// 		}

// 		if (result.positive) {
// 			$this.html(result.message); // replace form with message

// 		} else {
// 			$p = $("<p>").addClass(result.className).html(result.message);
// 			$feedback.html($p);
// 			grecaptcha.reset(); // ensure next form post is valid too
// 		}

// 		if (result.sent) {
// 			$this.get(0).reset();
// 			grecaptcha.reset(); // ensure next form post is valid too
// 		}

// 		if (result.reload) {
// 			location.reload(true); // true means force recache
// 		}
// 	});

//   }

//   var renderGoogleInvisibleRecaptcha = function() {
//     for (var i = 0; i < document.forms.length; ++i) {
//       var form = document.forms[i];
//       var holder = form.querySelector('.recaptcha-holder');
//       var ajax = (form.getAttribute("method") == "ajax");
//       if (null === holder){
//         continue;
//       }

//       (function(frm){
//         var holderId = grecaptcha.render(holder,{
//           'sitekey': '6LewIiYUAAAAAJcV-bQRfk824cYcsYwkIZ99Bpsy',
//           'size': 'invisible',
//           'badge' : 'inline', // possible values: bottomright, bottomleft, inline
//           'callback' : function (recaptchaToken) {
// 	if (ajax) {
// 		ajaxSubmit(frm);
// 	} else {
//             	HTMLFormElement.prototype.submit.call(frm);
//         	}
//           }
//         });

//         frm.onsubmit = function (evt){
//           evt.preventDefault();
//           grecaptcha.execute(holderId);
//         };

//       })(form);
//     }
//   };

// function bindAjaxSubmits() {

// 	$("form[method='ajax']").on("submit", function(e) {
// 		var $this = $(this), $feedback = $("div.output", $this), $container = $this.parent(), $submit = $("[type='submit']", $this);
// 		$feedback.empty();
// 		$submit.addClass("submitting");
// 		$.post($this.attr("action"), $this.serialize(), function (result) {

// 			$submit.removeClass("submitting");

// 			if (result.html) {
// 				$container.html(result.html);
// 				bindAjaxSubmits();
// 				return;
// 			}

// 			// update csrf if present
// 			if (result.csrf && $("input[name='csrf_token']", $this).length) {
// 				$("input[name='csrf_token']", $this).val(result.csrf);
// 			}
// 			// update feedback if present
// 			if (result.message && $feedback) {
// 				$feedback.addClass("row").append($("<label></label>"));
// 				$("<output>").addClass(result.className).val(result.message).appendTo($feedback);
// 			}

// 			if (result.redirect) {
// 				location.href = result.redirect;
// 			}

// 			if (result.reload) {
// 				location.reload(true); // true means force recache
// 			}
// 		});
// 		return false;
// 	});
// }

$(function () {

	$("#login-form-field-password").on("input", function(event) {
		var str = $(this).val().length ? "Log in" : "Send Password";
		$("#logon-form-submit").val(str);
	});

	// UIkit.util.on(document, 'hide', '[uk-alert]', function (event) {
	// 	$.post("/data/alert",{id: event.target.dataset.id});
	// });

	// $(".colorbox").colorbox({
	// 	onOpen: function () {
	// 		window.scrollTo(0,0);
	// 	},
	// 	fixed: true,
	// });

	// bindAjaxSubmits();

	$("#store_index").on("click", ".tile.app > figure", function (e) {
		$(this).closest(".tile").find("a:first").get(0).click();
	});


  // smooth scroll links starting with an octothorp
  $('a[href*="#"]')
  // Remove links that don't actually link to anything
  .not('[href="#"]')
  .not('[href="#0"]')
  .click(function(event) {
    // On-page links
    if (
      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
      &&
      location.hostname == this.hostname
    ) {
      // Figure out element to scroll to
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      // Does a scroll target exist?
      if (target.length) {
        // Only prevent default if animation is actually gonna happen
        event.preventDefault();
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000, function() {
          // Callback after animation
          // Must change focus!
          var $target = $(target);
          $target.focus();
          if ($target.is(":focus")) { // Checking if the target was focused
            return false;
          } else {
            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
            $target.focus(); // Set focus again
          };
        });
      }
    }
  });

	// $("#store_index .timeline-heading h4").on("click", function (e) {
	// 	var subject = $(this).text();
	// 	e.preventDefault();
	// 	$("textarea[name='your-message']", "#contact-form").val("Hi,\nI'm contacting about a service listed on your CourseSuite website - " + subject + "\n\nHere are my needs:\n-");
	// 	document.querySelector("#contact-form").scrollIntoView();
	// })



	$("a[href='#toggle-once']").each(function (index, el) {
		$(el).next().hide();
		$(el).on("click", function(e) {
			e.preventDefault();
			$(this).hide().next().fadeIn();
		});
	});

	// make store info page bundle tabs
	// $("[data-interaction='tabs']").on("click", "a[data-action='select-tab']", function (e) {
	// 	e.preventDefault();
	// 	var $this = $(this);
	// 	$this.addClass("active").siblings("a").removeClass("active");
	// 	$($this.attr("href")).addClass("active").siblings("div").removeClass("active");
	// });

	// // ajax load the contact form
	// $("#contact_form_placeholder").on("click", "a", function (e) {
	// 	e.preventDefault();
	// 	$("#contact_form_placeholder").load("/store/contactForm");
	// });

	$("a[data-action='dismiss-message']").on("click", function(e) {
		var $this = $(this);
		$.getJSON("/message/done/" + this.getAttribute("data-action-id"), function (result) {
			if (result.updated) {
				console.log("updated", $this);
				$this.closest("div.acknowledgement-item").fadeOut(250, function() { console.log("faded", this); $(this).remove() });
			}
		});
	});

	// youtube background video, but delay it a little
	setTimeout(function() {
		if ($("#bgndVideo").length) $("#bgndVideo").YTPlayer(); // .YTPApplyFilter('invert',100);
	}, 150);

	// turn text areas into markdown editors with integrated imgur upload
	$("textarea[data-markdown]").each(function (index, el) {
		el.simplemde = new SimpleMDE({
			element: el,
			spellChecker: false,
			placeholder: "Markdown / HTML is allowed.\nDrag images onto this editor to upload & link them\nTo nest markdown inside html, add attribute markdown=\"1\" of tags containing markdown.",
		});
		inlineAttachment.editors.codemirror4.attach(el.simplemde.codemirror, {
			uploadUrl: "https://api.imgur.com/3/image",
			extraHeaders: {
				Authorization: "Client-ID 662ce7a8f142394",
			},
			extraParams: {
				name: "Your image title",
				description: "Dragged onto editor using inline-attachment"
			},
			uploadFieldName: "image",
			onFileUploadResponse: function(xhr) {
				// "this" is now the inlineAttachment instance, not a XHR
				var result = JSON.parse(xhr.responseText),
					id = result.data.id,
					ext = result.data.type.split("/")[1],
					title = result.data.title || "Untitled",
					href = "https://i.imgur.com/" + id + "." + ext,
					src = "http://i.imgur.com/" + id + "m." + ext,
					newValue = "[![" + title + "](" + src + ")](" + href + ")";
				var text = this.editor.getValue().replace(this.lastValue, newValue);
				this.editor.setValue(text);

				// prevent internal upload
				return false;
			}
		});
	});

});

// general-purpose recursive, circular-reference-capable object search
var searchObject = function (object, matchCallback, currentPath, result, searched) {
    currentPath = currentPath || '';
    result = result || [];
    searched = searched || [];
    if (searched.indexOf(object) !== -1) {
        return;
    }
    searched.push(object);
    if (matchCallback(object)) {
        result.push({path: currentPath, value: object});
    }
    for (var property in object) {
        if (object.hasOwnProperty(property)) {
            searchObject(object[property], matchCallback, currentPath + "." + property, result, searched);
        }
    }
    return result;
}

// watch/callback for localised pricing data, which may or may not execute after DOMContentLoaded/load/onload/readyState etc
function fsCallbackFunction(data) {
console.dir(data);
  var fn = function() {
      [].forEach.call(document.querySelectorAll("[data-fsc-item-pricetotal-callback]"), function (node) {
        var dObj = searchObject(data,function(value) {
          return value!=null && value!=undefined && value.productPath===node.dataset.fscItemPath && value.length > 0;
        });
        // lets pull the price from the last in the series
        var format = node.dataset.format ? node.dataset.format : " - %price %currency";
        var price = dObj[dObj.length-1].value.unitPrice.replace(".00","");
        var text = format.replace("%price", price).replace("%currency", data.currency);
        node.innerHTML = text;
      });
    },
    t = function () {
      console.info(document.readyState);
      if(document.readyState==='complete') {
        fn();
      } else {
        setTimeout(t,100);
      }
    };
  t();
}