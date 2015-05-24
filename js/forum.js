var images = 0;
$(function() {

	$("#addImage").click(function(event) {
		event.preventDefault();
		
		++images;
		
		if (images > 5) {
			return;
		}
		
		if (images == 5) {
			$("#addImage").remove();
		}
		
		var div = $("<div></div>");
		div.addClass('image-picker')
		var picker = $("<input></input>");
		picker.attr('type', 'file');
		picker.attr('name', 'image[]');
		div.append(picker);
		$("#pickers").append(div);
		picker.click();
		
		if (images == 1) {
			$("#addImage").html("Add another image");
		}
		
		
		
	});
	
});
