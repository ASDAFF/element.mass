/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */
// Plugin load handler
$(document).delegate(hid+' input[data-role="with_discounts"]', 'change', function(e){
	var
		divSiteId = $('select[data-role="site_id"]', divSiteId).closest('.plugin-form__field');
	if($(this).is(':checked')){
		divSiteId.show();
	}
	else{
		divSiteId.hide();
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('input[data-role="with_discounts"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="price_type"]', div).val().length){
		return $('input[data-role="error_no_price_type_field"]', div).val();
	}
	else if(!$('select[data-role="site_id"]', div).val()){
		return $('input[data-role="error_no_site_field"]', div).val();
	}
	else if(!$('select[data-role="target_field"]', div).val()){
		return $('input[data-role="error_no_destination_field"]', div).val();
	}
	return true;
});
