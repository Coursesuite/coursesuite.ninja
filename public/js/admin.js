$(function () {

	$("[sortable]").each(function(index, el) {
		Sortable.create(el, {
			animation: 150,
			handle: ".my-handle",
			onStart: function (evt) {
				$(evt.item).find("[data-id]").trigger("click");
			},
			onEnd: function (evt) {
				// var itemEl = evt.item;  // dragged HTMLElement
				// console.log(evt, itemEl);

				// set the sorting field to match the order of the tab list elements
				Array.from(document.querySelectorAll("div.tab-bar > ul[sortable] > li")).map(function (li,order) {
					document.querySelector("div.tab-bar div[id='tab_" + li.querySelector("[data-id]").dataset.id + "'] ").dataset.sort = order;
				});
				var nodes = Array.from(document.querySelectorAll("div.tab-bar div[id]")),
					container = document.querySelector("div.tab-bar div.tabs");

				// sort the array representing the dom nodes based on the sorting field
				nodes.sort(function (a, b) {
					var x = ~~a.dataset.sort,
						y = ~~b.dataset.sort;
					return (x<y) ? -1 : (x>y) ? 1 : 0;
				});

				// re-append the nodes to the dom (moves them)
				for (var i=0;i<nodes.length;++i) {
					container.appendChild(nodes[i]);
				}

				// now a form post of array values will be in sorted order
			}
		});
	});
	$("div.tab-bar li > [data-id]").on("click", function (e) {
		$(e.target).closest("li").addClass("active").siblings().removeClass("active");
		var $tab = $("#tab_" + e.target.dataset.id);
		$tab.addClass("active").siblings().removeClass("active");

		// codemirror can't start blank, you have to refresh it
		var cm = $tab.find("textarea[data-markdown]").get(0).simplemde;
		cm.codemirror.refresh();

	});

	$('#upload_widget_opener').cloudinary_upload_widget({
		cloud_name: 'coursesuite',
		upload_preset: 'ietcpiwn',
		cropping: 'server',
		cropping_show_dimensions: true,
		folder: 'coursesuite',
		sources: ['local','url','camera'],
	},
    function(error, result) {
    	console.log(error, result)
    });

	$("[data-sortable]").each(function (el, index) {
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

	$("input[data-action='previewEditor']").on("click", function (e) {
		e.preventDefault();
		var html = $("textarea#content").val(),
			$body = $("body");
		$body.removeClass("no-scroll");
		window.scrollTo(0, 0);
		$("#preview").remove();
		document.querySelector("body").scrollTop = 0;
		$("<div id='preview'><div class='content'><iframe src='about:blank' width='100%' height='100%' frameborder='0' id='editorPreview'></iframe></div>").on("click", function (e) {
			$("#preview").addClass("closing");
			$body.removeClass("no-scroll");
			setTimeout(function() { $("#preview").remove() }, 250);
		}).appendTo($body.addClass("no-scroll"));
		var doc = document.getElementById("editorPreview").contentWindow.document;
		doc.open();
		doc.write(html);
		doc.close();
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

})