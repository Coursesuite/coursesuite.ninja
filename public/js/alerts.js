function acknowledge(value) {
	$.post("/me/acknowledge/", {id: value});
}