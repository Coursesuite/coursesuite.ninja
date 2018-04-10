// a slideshow
(function (window, $, undefined) {

	if (typeof window.slides == 'undefined') return;

	var _currentSlide = 0,
		_aATimeout,
		_autoAdvance = true,
		_mobile = (typeof window.orientation !== 'undefined'),
		$Container = $("#slideshow"),
		$Display = $("#current_slide"),
		$Thumbs = $("#slide_controls"),
		_slideWidth = 0,
		_slideTime = 6789;

	// mouse over
	$Container.on("mouseenter mouseover", function () {
	    _autoAdvance = false;
	    if (_aATimeout) clearTimeout(_aATimeout);
	});

	// swipe
	(new Hammer($Display.get(0))).on("swipeleft", function (e) {
		    _autoAdvance = false;
		slideshow("advance");
	}).on("swiperight", function (e) {
		    _autoAdvance = false;
		slideshow("precede");
	});

	// navigation
	function slideAdvance() {
		if (!_autoAdvance) return;
		if (_aATimeout) clearTimeout(_aATimeout);
		slideshow("advance");
		_aATimeout = setTimeout(function(){slideAdvance()},_slideTime);
	}

	// init
	window.addEventListener("load", function () {
		_slideWidth = $Container.outerWidth();

		$("figure.slide img", $Display).each(function() {
			$(this).css({
				"width": _slideWidth + "px",
				"max-height": "424px" // "344px"
			});
		});
		if (_mobile) {
			console.log("mobile width", _slideWidth);
			$("#store_info article .store-item-specific section.info > div").css({
				"width": _slideWidth + "px"
			});
		}

		// glow the first slide
		glow();

		// start the slideshow
		_aATimeout= setTimeout(function(){slideAdvance()},_slideTime);
	});

	// set the shadow colour to the most common colour of the thumbnail
	function glow() {
		if (window.slides[_currentSlide] && window.slides[_currentSlide].bgcolor) {
			var bg = window.slides[_currentSlide].bgcolor;
			var rgb = (bg.indexOf("rgba")!==-1) ? "rgb(" + bg.substring(bg.lastIndexOf("(")+1,bg.lastIndexOf(",")) + ")" : bg;

			$("div.slide-wrapper", $Container).css("box-shadow","inset 0 0 250px " + bg);

			if (bg.indexOf("rgba")!==-1) {
				bg = "linear-gradient(to bottom, rgba(0,0,0,.5) 90%, rgba(" + bg.substring(bg.lastIndexOf("(")+1,bg.lastIndexOf(",")) + ", 0.9) 100%)";
				$("div.slide-wrapper .slide.active>figcaption", $Container).css("background", bg);
			}
		}
	}

	// advance the slide
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
		}
		var n = (index * _slideWidth),
			m = Math.max((index * (120 + 9) - 60), 0); // half a thumb on the left, plus margin between thumbs, plus 2 times the border width, plus a full thumb visible, no matter the index
		$Display.css({
			"transform": "translateX(-" + n + "px)"
		}).children(".slide").removeClass("active").filter(":eq(" + index + ")").addClass("active");

		$Thumbs.css({
			"transform": "translateX(-" + m + "px)"
		}).find("a").removeClass("active");

		$("a:eq(" + index + ")", $Thumbs).addClass("active");

		_currentSlide = index;

		glow();

	}

	function show(index) {
		var slide = window.slides[index];
		if ('ontouchstart' in window) {
			window.open((slide.video) ? slide.video : slide.image);
		} else {
			var html = "<span />", style = "", $body = $("body");
			window.scrollTo(0,0);
			$body.removeClass("no-scroll");
			$("#preview").remove();
			document.querySelector("body").scrollTop = 0;
			if (slide.video) {
				html = "<iframe src='" + slide.video + "' width='100%' height='100%' frameborder='0'></iframe>";
			}
			if (slide.image) {
				style = " style='background-image:url(" + slide.image + ")'";
			}
			$("<div id='preview'><div class='content'" + style + ">" + html + "</div>").on("click", function (e) {
				$("#preview").addClass("closing");
				$body.removeClass("no-scroll");
				setTimeout(function() { $("#preview").remove() }, 250);
			}).appendTo($body.addClass("no-scroll"));
			if (slide.bgcolor) {
				$("#preview").css("background-color", slide.bgcolor);
			}
		}
	}

	function keepGoing() {
		_autoAdvance = true;
		slideAdvance();
	}

	window.gotoSlide = slideshow;
	window.expandSlide = show;
	window.keepGoing = keepGoing;

})(window, jQuery);