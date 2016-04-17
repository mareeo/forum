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
	var writer = new commonmark.HtmlRenderer({
		sourcepos: true,
		safe: true
	});

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
		var stylesheet = $('#stylesheet').attr('href');
		stylesheet = stylesheet.replace('style.css', 'style-invert.css');
		$('#stylesheet').attr('href', stylesheet);
	}

	$("#changeTheme").click(function() {
		var stylesheet = $('#stylesheet').attr('href');
		console.log(stylesheet);
		if(localStorage.getItem('theme') == 'dark') {
			localStorage.setItem('theme', 'light');
			stylesheet = stylesheet.replace('style.css', 'style-invert.css');
			$('#stylesheet').attr('href', '/forum/css/style-invert.css');
		} else {
			localStorage.setItem('theme', 'dark');
			stylesheet = stylesheet.replace('style-invert.css', 'style.css');
		}
		console.log(stylesheet);
		$('#stylesheet').attr('href', stylesheet);

	});

	$("form").submit(function(event) {

		var subject =  $(event.target).find('input[name="subject"]').val();

		if(subject.length == 0) {
			alert("You must enter a subject");
			event.preventDefault();
		}
	});


	
});


