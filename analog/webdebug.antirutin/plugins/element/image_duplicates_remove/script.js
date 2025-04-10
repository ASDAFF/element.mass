/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	wdaSelect2($('select[data-role="method"]', div));
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="property"] option:not([value=""]):selected', div).length){
		return $('input[data-role="error_no_property"]', div).val();
	}
	else if(!$('div[data-role="duplicate_methods"] input[type="checkbox"]:checked', div).length){
		return $('input[data-role="error_no_methods"]', div).val();
	}
	return true;
});
