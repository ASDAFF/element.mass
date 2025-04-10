/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */
// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('select[data-role="source"]', div).trigger('change');
	$('select[data-role="target"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){

	if(!$('select[data-role="source"]', div).val()
		|| $('select[data-role="source"]').find('option:selected').attr('data-type')!= 'F'){
		return $('input[data-role="error_no_source_field"]', div).val();
	}
	if(!$('select[data-role="target"]', div).val()
		|| $('select[data-role="target"]').find('option:selected').attr('data-type')!= 'F'){
		return $('input[data-role="error_no_target_field"]', div).val();
	}
	return true;
});
