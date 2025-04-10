/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// 
$(document).delegate(hid+' input[type="checkbox"][data-role="additional_filter"]', 'change', function(e){
	var divFilterPhp = $('textarea[data-role="additional_filter_php"]').closest('.plugin-form__field');
	if($(this).is(':checked')){
		divFilterPhp.show();
	}
	else{
		divFilterPhp.hide();
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('input[type="checkbox"][data-role="additional_filter"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="field"]', div).val()){
		return $('input[data-role="error_no_field"]', div).val();
	}
	return true;
});

