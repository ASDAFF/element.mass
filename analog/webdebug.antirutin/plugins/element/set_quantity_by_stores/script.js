/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */


$(document).delegate(hid+' input[data-role="save_to_property"]', 'change', function(e){
	let
		divSelect = $(this).closest('.plugin-form__field').find('[data-role="save_property"]').parent();
		divSelect.toggle($(this).prop('checked'));
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('input[data-role="save_to_property"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="stores"] option:not([value=""]):selected', div).length){
		return $('input[data-role="error_no_stores"]', div).val();
	}
	return true;
});
