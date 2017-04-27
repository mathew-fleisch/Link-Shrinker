var clipboard = new Clipboard('#copy-btn');
var ANIMATION_SPEED = 100;
var INFO_TIMEOUT = 5; //seconds
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
					// console.log('Success!', json);
					if(json['success']) {
						$('#errors').slideUp(ANIMATION_SPEED);
						var short_url = 
							location.protocol+'//'
							+window.location.hostname+(location.port ? ':'+location.port: '')
							+'/a/'+json['alias'];
						$('#short-url').val(short_url);
						$('#visit-count').html(json['existing']);
						$('#lazy-link').html('<a href="'+short_url+'" target="_blank" onClick="add_visitor();">'+short_url+'</a>');

						if(input.length < short_url.length && json['existing'] < 1) { 
							show_info('info', 'Technically this url is not shorter... but we made it for you anyway.');
						}
					} else { 
						show_info('error', (json['message']));
					}
				}
			});
			$('#output-container').slideDown(ANIMATION_SPEED);
		} else {
			// console.error('Invalid url...');
			show_info('error', ('Invalid url...'));
		}
	});

	$('#long-url').keydown(function (e){ 
		//Key codes for arrows, control (keys that don't make a character)
		var modifiers = [9,16,17,18,20,37,38,39,40,91,93];
		var code = parseInt(e.keyCode);
		if(code == 13) { // Enter key 
				$('#shrink').click(); 
		} else {
			//Hide output container if any non-modifier key or non-arrow is pressed
			if(modifiers.indexOf(code) === -1) {
				$('#output-container').slideUp(ANIMATION_SPEED);
			}
		}
	});
	$(document).on('click', '#short-url-label', function() { $('#copy-btn').click(); });

});
function add_visitor() {
	var count = parseInt($('#visit-count').html());
	$('#visit-count').html(count+1);
}
function show_info(type, msg, hide_in = INFO_TIMEOUT) { 
	hide_in = hide_in * 1000;
	var alert_type = '';
	switch(type) {
		case 'error': alert_type = 'danger'; break;
		default: 
		case 'info':  alert_type = 'info';   break;
	}
	$('#errors').html(
		'<div class="alert alert-dismissible alert-'+alert_type+'">'
		  +'<button type="button" class="close" data-dismiss="alert">&times;</button>'
		  +(type == 'error' ? '<strong>Oh snap!</strong> ' : '')+msg
		+'</div>'
	).slideDown(ANIMATION_SPEED);

	if(type == 'error') {
		$('#output-container').slideUp(ANIMATION_SPEED).hide();
	} else { 
		setTimeout(function() {
			$('#info-container').slideUp(ANIMATION_SPEED).hide();
		}, hide_in);
	}
}