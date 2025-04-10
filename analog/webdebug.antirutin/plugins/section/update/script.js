/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check box 'iblock_section_update'
$(document).delegate(hid+' input[data-role="iblock_section_update"]', 'change', function(e){
	var
		divWithFields = $('input[data-role="iblock_section_update_with_fields"]', div).closest('.plugin-form__group');
	if($(this).is(':checked')){
		divWithFields.show();
	}
	else{
		divWithFields.hide();
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('input[data-role="iblock_section_update"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	var
		sectionUpdate = $('input[data-role="iblock_section_update"]', div).is(':checked');
	if(!sectionUpdate){
		return $('input[data-role="error_nothing_selected"]', div).val();
	}
	return true;
});