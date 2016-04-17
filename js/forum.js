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
		
		var div = $("<div>");
		div.addClass('image-picker');
		var picker = $("<input>");
		picker.attr('type', 'file');
		picker.attr('name', 'image[]');
		div.append(picker);
		$("#pickers").append(div);
		picker.click();
		
		if (images == 1) {
			$("#addImage").html("Add another image");
		}
	});

	var reader = new commonmark.Parser();
	var writer = new commonmark.HtmlRenderer();

	var renderPreview = function(event) {
		var value = $(event.target).val();
		var parsed = reader.parse(value);
		var result = writer.render(parsed);
		$('#messagePreview').html(result);
	};

	var debounced = $.debounce(500, renderPreview);

	$("textarea[name='message']").keyup(debounced);

	if(localStorage.getItem('theme') == null) {
		localStorage.setItem('theme', 'dark');
	}

	if(localStorage.getItem('theme') == 'light') {
		$('#stylesheet').attr('href', '/forum/css/style-invert.css');
	}

	$("#changeTheme").click(function() {
		if(localStorage.getItem('theme') == 'dark') {
			localStorage.setItem('theme', 'light');
			$('#stylesheet').attr('href', '/forum/css/style-invert.css');
		} else {
			localStorage.setItem('theme', 'dark');
			$('#stylesheet').attr('href', '/forum/css/style.css');
		}

	})


	
});


