/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check box 'iblock_element_update'
$(document).delegate(hid+' input[data-role="iblock_element_update"]', 'change', function(e){
	var
		divWithFields = $('input[data-role="iblock_element_update_with_fields"]', div).closest('.plugin-form__group');
	if($(this).is(':checked')){
		divWithFields.show();
	}
	else{
		divWithFields.hide();
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('input[data-role="iblock_element_update"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	var
		productUpdate = $('input[data-role="catalog_product_update"]', div).is(':checked'),
		elementUpdate = $('input[data-role="iblock_element_update"]', div).is(':checked');
	if(!productUpdate && !elementUpdate){
		return $('input[data-role="error_nothing_selected"]', div).val();
	}
	return true;
});