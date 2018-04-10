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

});
