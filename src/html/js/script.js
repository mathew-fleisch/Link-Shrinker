var clipboard = new Clipboard('#copy-btn');
var ANIMATION_SPEED = 100;
$(document).ready(function() {

	$(document).on('click', '#shrink', function() {
		var input = $('#long-url');
		var ssl = false;
		if(isValidURL(input.val())) { 
			var url = encodeURIComponent($('#long-url').val());
			$.ajax({
				url: '/api/url',
				type: 'POST',
				data: {
					url: url
				},
				success: function(data) {
					// var json = JSON.parse(decodeURIComponent(data));
					var json = data;
					console.log('Success!', json);
					if(json['success']) {
						$('#errors').slideUp(ANIMATION_SPEED);
						var short_url = 
							location.protocol+'//'
							+window.location.hostname+(location.port ? ':'+location.port: '')
							+'/a/'+json['alias'];
						$('#short-url').val(short_url);
						$('#visit-count').html(json['existing']);
						$('#lazy-link').html('<a href="'+short_url+'" target="_blank">'+short_url+'</a>');
					} else { 
						throw_error(json['message']);
					}
				}
			});
			$('#output-container').slideDown(ANIMATION_SPEED);
		} else {
			// console.error('Invalid url...');
			throw_error('Invalid url...');
		}
	});

	$('#long-url').keydown(function (e){ if(e.keyCode == 13){ $('#shrink').click(); } else { $('#output-container').slideUp(ANIMATION_SPEED); }});
	$(document).on('click', '#short-url-label', function() { $('#copy-btn').click(); });

});
function throw_error(msg) {
	$('#output-container').slideUp(ANIMATION_SPEED);
	$('#errors').html(
		'<div class="alert alert-dismissible alert-danger">'
		  +'<button type="button" class="close" data-dismiss="alert">&times;</button>'
		  +'<strong>Oh snap!</strong> '+msg
		+'</div>'
	).slideDown(ANIMATION_SPEED);
}