$(function () {
	console.log($("select[data-action='update-form-action']"));
	$("select[data-action='update-form-action']").on("change", function () {
alert(this.value);
		if (this.value) {
			$(this).closest("form").attr("action", this.value);
		}
	});
});