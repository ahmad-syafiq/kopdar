_Bbc(function ($) {
	function panelEditOpen() {
		if ($("#panel_button").length > 0) {
			localStorage.setItem("panel_edit_show", 1);
			$("#panel_list").removeClass("full").addClass("split");
			$("#panel_button").removeClass("show");
			$("#panel_edit").addClass("show");
		}
	}

	function panelEditClose() {
		localStorage.removeItem("panel_edit_show");
		$("#panel_list").removeClass("split").addClass("full");
		$("#panel_button").addClass("show");
		$("#panel_edit").removeClass("show");
	}

	$("#panel_button").on("click", panelEditOpen);
	$("#panel_edit").on("click", "button[type='reset']", panelEditClose);

	if (localStorage.getItem("panel_edit_show") == 1) {
		panelEditOpen();
	}
});