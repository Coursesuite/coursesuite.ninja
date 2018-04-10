var CONST_IMGUR_UPLOAD = {
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
		},
	CONST_INTERNAL_UPLOAD = {
		uploadUrl: "/admin/uploadMDE/"
	};

$(function() {
	$("textarea[data-markdown]").each(function (index, el) {
		el.simplemde = new SimpleMDE({
			element: el,
			spellChecker: false,
			placeholder: "Markdown / HTML is allowed.\nDrag images onto this editor to upload & link them\nTo nest markdown inside html, add attribute markdown=\"1\" of tags containing markdown.",
			toolbar: ["bold","italic","heading","|","code","quote","unordered-list","ordered-list","table","|","link","image",{
				name: "slideshow",
				action: function customFunction(editor) {
					var txt = editor.codemirror.getSelection().trim();
					if (txt.length === 0 || txt.indexOf("![") === -1) { alert("Drag one or more images to the editor and then select them (triple-click the line they are on), then press this button again."); return; }
					var anim = ["slide","fade","scale"],
						html = ["<div uk-lightbox='animation:" + anim[Math.floor(Math.random() * anim.length)] + "' class='uk-child-width-1-3@m uk-child-width-1-1@s uk-child-width-1-4@xl' uk-grid>"],
						images = txt.split("![");
					for (var i=1;i<images.length;i++) {
						var img = images[i];
						html.push(" <div>");
						var md = img.replace(")","").split("](");
						html.push("  <a class='uk-inline uk-box-shadow-small uk-box-shadow-hover-large' caption='" + md[0] + "' href='" + md[1] + "'>" +
									"<img src='/content/image/" + btoa(md[1].replace(location.origin,"")) + "/270/' alt='" + md[0] + "'>" +
								  "</a>");
						html.push("</div>");
					}
					html.push("</div>");
					editor.codemirror.replaceSelection(html.join("\n"));
				},
				className: "fa fa-fast-forward",
				title: "Lightbox / Slideshow"
			},"|","preview","side-by-side","fullscreen","|","guide"],

		});
		inlineAttachment.editors.codemirror4.attach(el.simplemde.codemirror, CONST_INTERNAL_UPLOAD);
	});
});


// let createIFrame = (lang,src) => {
//     let selectorName = langToSelectorName[lang];
//     return `<iframe sandbox="allow-scripts" height="100px" width="100%" srcdoc='
//     <pre><code class=&quot;klipse&quot;>${src}</code></pre>

//       <link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;https://storage.googleapis.com/app.klipse.tech/css/codemirror.css&quot;>

//       <script>
//         window.klipse_settings = {
//         ${selectorName}: &quot;.klipse&quot;
//         };
//       </script>
//       <script src=&quot;${jsSrc(lang)}&quot;></script>
//     '>
//     </iframe>`
// };

//     let klipsify = (elem) => {
//         elem.innerHTML = createIFrame(elem.dataset.language, elem.innerHTML);
//         return elem;
//     }

//     document.querySelectorAll('klipse-snippet').forEach(x => klipsify(x))
// }