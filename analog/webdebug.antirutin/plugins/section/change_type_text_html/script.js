/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="type_field"]', 'change', function(e){
	$('select[data-role="html_to_text"]').closest('.plugin-form__field').toggle($(this).val() == 'TEXT');
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('select[data-role="type_field"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="source_field"]', div).val()){
		return $('input[data-role="error_no_source_field"]', div).val();
	}
	return true;
});