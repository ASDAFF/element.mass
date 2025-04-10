/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="settings_type"]', 'change', function(e){
	var customSettings = $('div[data-role="custom_code_settings"]', div);
	if($(this).val() == 'custom'){
		$(':input', customSettings).removeAttr('disabled');
	}
	else{
		$(':input', customSettings).attr('disabled', 'disabled').each(function(){
			if($(this).is('input[type="text"]') || $(this).is('select')){
				if($(this).attr('data-default') != undefined){
					$(this).val($(this).attr('data-default'));
				}
			}
			else if ($(this).is('input[type="checkbox"]')){
				$(this).prop('checked', $(this).attr('data-default') == 'Y');
			}
		});
		$('input[data-role="settings_unique"]', div).trigger('change');
		$('input[data-role="settings_translit"]', div).trigger('change');
	}
});

$(document).delegate(hid+' input[data-role="settings_unique"]', 'change', function(e){
	var
		translitSettings = $('div[data-role="settings_unique_sub"]', div).hide();
	if($(this).is(':checked')){
		translitSettings.show();
	}
});

$(document).delegate(hid+' input[data-role="settings_translit"]', 'change', function(e){
	var
		translitSettings = $('div[data-role="settings_translit_sub"]', div).hide();
	if($(this).is(':checked')){
		translitSettings.show();
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	// Handlers
	$('select[data-role="settings_type"]', div).trigger('change');
	$('select[data-role="settings_case"]', div).trigger('change');
	$('select[data-role="exists_action"]', div).trigger('change');
	// Initial click on checkbox 'Unique'
	$('input[data-role="settings_unique"]', div).trigger('change');
	// Initial click on checkbox 'Translit'
	$('input[data-role="settings_translit"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="source_field"]', div).val()){
		return $('input[data-role="error_no_source_field"]', div).val();
	}
	return true;
});
