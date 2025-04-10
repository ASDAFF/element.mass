/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	wdaSelect2($('select[data-role="method"]', div));
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="field"] option:not([value=""]):selected', div).length){
		return $('input[data-role="error_no_field"]', div).val();
	}
	else{
		var
			width = parseInt($('input[data-role="width"]', div).val()),
			height = parseInt($('input[data-role="height"]', div).val());
		if(isNaN(width) || width <= 0){
			return $('input[data-role="error_no_width"]', div).val();
		}
		else if (isNaN(height) || height <= 0){
			return $('input[data-role="error_no_height"]', div).val();
		}
	}
	return true;
});
