function fsPopupClosed(evnt) {
	if (evnt) {
		fastspring.builder.reset();
		var feedback = UIkit.modal.dialog('<p class="uk-modal-body">Validating purchase information, please wait a moment ...</p>', {
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