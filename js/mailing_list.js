$(document).ready(function(){


	$('#new_member_form').submit(function(event){
		if ($('#new_member_name').val().length === 0 ) {
			event.preventDefault();
			alert('You must enter a name for member');
		} else if ($('#new_member_email').val().length === 0 ) {
			event.preventDefault();
			alert('You must enter an email for member');
		}
	});


	function applyMultiplySelect(element) {
		if($(element).attr('class') == 'member_lists'){
			var checkHandeler=function(checked){
				var member_id=element.attr('data-member_id');
				if (member_id) {
					$.post(OC.filePath('mailing_list','ajax','togglelists.php'),
						{member_id:member_id,checked:checked},
						function(data){
							console.log(data)
						}, "json"
					);
				}
			};
			var addLists = function(group) {
				$('select[multiple]').each(function(index, element) {
					if ($(element).find('option[value="'+group +'"]').length == 0) {
						$(element).append('<option value="' + escapeHTML(group) + '">' + escapeHTML(group) + '</option>');
					}
				})
			};
			var label;

			element.multiSelect({
				createCallback:addLists,
				createText:label,
				oncheck:checkHandeler,
				onuncheck:checkHandeler,
				minWidth: 100,
			});
		}
	}


	$('select[multiple]').each(function(index,element){
		applyMultiplySelect($(element));
	});

	$('#upload_vcf_form').submit(function(event) {
		if (window.FormData !== undefined) {
			event.preventDefault();
			var file = $('#import_from_vcf')[0].files[0];
			var data = new FormData(document.getElementById('upload_vcf_form'));
			$.ajax({
				url: OC.filePath('mailing_list','ajax','upload_vcards.php'), 
				type: "POST", 
				data: data, 
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function(data){
					console.log(data)
				}
			});
		}
	});
	
	
	$('td.remove>a').live('click',function(event){
		$(this).parent().parent().hide();
		// Call function for handling delete/undo
		$.post(OC.filePath('mailing_list', 'ajax', 'remove_member.php'), "member_id=" +$(this).attr('data-member_id'), function(data) {
				//console.log(data);
			}, 'json'
		);
	});

	
	
});