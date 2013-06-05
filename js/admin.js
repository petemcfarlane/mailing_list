$(document).ready(function(){


	$('#mailing_list').submit(function(event) {
		if ( $('#add_mailing_list_name').val().length > 0 ) {
		} else {
			event.preventDefault();
			var post = $(this).serialize();
			$.post( OC.filePath('mailing_list', 'ajax', 'mailing_list.php'), post, function(data) {
			}, "json");
		}
	})

	
	$('.mailing_list_names').blur(function(event) {
		event.preventDefault();
		var post = $(this).serialize();
		$.post( OC.filePath('mailing_list', 'ajax', 'mailing_list.php'), post, function(data) {
		}, "json");
	});


	$('.remove_mailing_list').on("click", function(event) {
		event.preventDefault();
		if ( confirm("Are you sure you wish to remove the list?") ) {
			var post = 'mailing_list_remove_id='+$(this).attr('data-mailing_list_id');
			$.post( OC.filePath('mailing_list', 'ajax', 'mailing_list.php'), post, function(data) {
				$('*[data-list-id="' + data + '"]').hide('fast');
			}, 'json');
		}
	});

});
