/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="mode_source"]', 'change', function(e){
	var
		value = $(this).val(),
		modeQuantity = 5,
		divPrices = $('select[data-role="price_type"]', div).closest('.plugin-form__field').hide();
	if(value != modeQuantity){
		divPrices.show();
	}
});

$(document).delegate(hid+' select[data-role="target"]', 'change', function(e){
	var
		option = $('option:selected', this),
		type = option.attr('data-type'),
		divModeText = $('select[data-role="mode_text"]', div).closest('.plugin-form__field').hide(),
		divModeNumeric = $('select[data-role="mode_numeric"]', div).closest('.plugin-form__field').hide(),
		divModeList = $('select[data-role="mode_list"]', div).closest('.plugin-form__field').hide();
	if(option.val().length){
		if(type == 'S'){
			divModeText.show();
		}
		else if(type == 'N'){
			divModeNumeric.show();
		}
		else{
			divModeList.show();
		}
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('select[data-role="mode_source"]', div).trigger('change');
	$('select[data-role="target"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	var
		mode = $('select[data-role="mode_source"]', div).val(),
		modeQuantity = 5;
	if(mode != modeQuantity && !$('select[data-role="price_type"] option:not([value=""]):selected', div).length){
		return $('input[data-role="error_no_source_price"]', div).val();
	}
	else if(!$('select[data-role="target"]', div).val()){
		return $('input[data-role="error_no_target_field"]', div).val();
	}
	return true;
});