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
	return true;
});
