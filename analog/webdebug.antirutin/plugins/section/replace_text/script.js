/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="mode"]', 'change', function(e){
	var
		mode = $(this).val();
	$('div[data-role="mode_settings"]', div).hide().filter('[data-code="'+mode+'"]').show();
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	// Handlers
	$('select[data-role="mode"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="field"]', div).val()){
		return $('input[data-role="error_no_field"]', div).val();
	}
	else{
		var mode = $('select[data-role="mode"]', div).val();
		if(mode == 'simple' && !$('textarea[data-role="simple_search"]', div).val().length){
			return $('input[data-role="error_no_simple_search"]', div).val();
		}
		else if(mode == 'reg_exp' && !$('textarea[data-role="reg_exp_search"]', div).val().length){
			return $('input[data-role="error_no_reg_exp_search"]', div).val();
		}
		else if(mode == 'append' && !$('textarea[data-role="append_text"]', div).val().length){
			return $('input[data-role="error_no_append_text"]', div).val();
		}
		else if(mode == 'prepend' && !$('textarea[data-role="prepend_text"]', div).val().length){
			return $('input[data-role="error_no_prepend_text"]', div).val();
		}
	}
	return true;
});
