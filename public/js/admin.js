$(function () {

	// turn text areas into markdown editors with integrated imgur upload
	$("textarea[data-markdown]").each(function (index, el) {
		el.simplemde = new SimpleMDE({
			element: el,
			spellChecker: false,
			placeholder: "Markdown / HTML is allowed.\nDrag images onto this editor to upload & link them\nTo nest markdown inside html, add attribute markdown=\"1\" of tags containing markdown.",
		});
		inlineAttachment.editors.codemirror4.attach(el.simplemde.codemirror, {
			// uploadUrl: "/admin/uploadMDE/"
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
	}),

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