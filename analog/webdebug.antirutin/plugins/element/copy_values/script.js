/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="source"]', 'change', function(e){
	var
		settingsAll = $(this).closest('.plugin-form__field').children('div[data-role="group_settings_source"]').hide(),
		option = $(this).find('option:selected'),
		typeFull = option.attr('data-type') ? option.attr('data-type') : '';
		type = typeFull.split(':')[0],
		code = option.attr('value'),
		divTarget = settingsAll.filter('[data-type="'+typeFull+'"],[data-type="'+type+'"],[data-code="'+code+'"]').show();
	//
	var domain = $('input[data-role="site_domain"]', div).closest('.plugin-form__group').hide();
	if(code == 'DETAIL_PAGE_URL'){
		domain.show();
	}
	else if (option.attr('data-type') == 'F'){
		$('select[data-role="source_f"]', div).trigger('change');
	}
	//
	var divTagsMultiple = $('input[data-role="tags_multiple"]', div).closest('.plugin-form__group').hide();
	if(code == 'TAGS'){
		divTagsMultiple.show();
	}
	//
	if(code.match(/^(OFFERS.)?CATALOG_(PRICE|CURRENCY)_(\d)(__|)(.*?)$/)){
		var divDelta = $('input[data-role="optimal_price_delta"]', div).closest('.plugin-form__field').hide();
		settingsAll.filter('[data-code="price_settings"]').show();
		if(type == 'N'){
			divDelta.show();
		}
	}
	//
	if(div.is(':visible') && !divTarget.is(':visible')){
		divTarget.find('option').prop('selected', false).closest('select').trigger('change');
	}
	//
	wdaToggleSettingsOptimalPriceSiteId();
	wdaToggleSettingsMultiple();
	wdaToggleSettingsFileConvertName();
});

$(document).delegate(hid+' select[data-role="source_f"]', 'change', function(e){
	var domain = $('input[data-role="site_domain"]', div).closest('.plugin-form__group').hide();
	if($(this).val() == 'src_url'){
		domain.show();
	}
});

$(document).delegate(hid+' select[data-role="target"]', 'change', function(e){
	wdaToggleSettingsMultiple();
	wdaToggleSettingsHtmlType();
	wdaToggleSettingsSaveCurrentValues();
	wdaToggleSettingsRemoveDuplicates();
	wdaToggleSettingsFileConvertName();
	wdaToggleSettingsCopyRawElementId();
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('select[data-role="source"]', div).trigger('change');
	$('select[data-role="target"]', div).trigger('change');
});

function wdaToggleSettingsHtmlType(){
	var
		divField = $('select[data-role="target"]', div).closest('.plugin-form__field')
			.children('div[data-role="group_target_settings"][data-code="html_type"]').hide(),
		option = $('select[data-role="target"] option:selected', div),
		typeFull = option.attr('data-type');
	if(typeFull == 'S:HTML'){
		divField.show();
	}
	if(!divField.is(':visible')){
		$('option', divField).prop('selected', false).closest('select').trigger('change');
	}
}

function wdaToggleSettingsOptimalPriceSiteId(){
	var
		divField = $('select[data-role="source"]', div).closest('.plugin-form__field')
			.children('div[data-role="group_settings_source"]').find('select[data-role="optimal_price_site_id"]')
			.closest('.plugin-form__field').hide(),
		option = $('select[data-role="source"] option:selected', div),
		code = option.attr('value');
		typeFull = option.attr('data-type');
	if(match = code.match(/^(OFFERS.)?CATALOG_(PRICE|CURRENCY)_(\d)(__|)(.*?)$/)){
		if(!(match[2] == 'PRICE' && match[5] == '')){
			divField.show();
		}
	}
}

function wdaToggleSettingsMultiple(){
	var
		divField = $('select[data-role="target"]', div).closest('.plugin-form__field')
			.children('div[data-role="group_target_settings"][data-code="multiple_mode"]').hide(),
		optionSource = $('select[data-role="source"] option:selected', div),
		optionTarget = $('select[data-role="target"] option:selected', div),
		multipleSource = optionSource.attr('data-multiple') == 'Y';
		multipleTarget = optionTarget.attr('data-multiple') == 'Y';
	if(multipleSource && !multipleTarget){
		divField.show();
	}
}

function wdaToggleSettingsSaveCurrentValues(){
	var
		divField = $('select[data-role="target"]', div).closest('.plugin-form__field')
			.children('div[data-role="group_target_settings"][data-code="save_current_values"]').hide(),
		optionTarget = $('select[data-role="target"] option:selected', div),
		multipleTarget = optionTarget.attr('data-multiple') == 'Y';
	if(multipleTarget){
		divField.show();
	}
	if(!divField.is(':visible')){
		$('input[type="checkbox"]', divField).prop('checked', false);
	}
}

function wdaToggleSettingsRemoveDuplicates(){
	var
		divField = $('select[data-role="target"]', div).closest('.plugin-form__field')
			.children('div[data-role="group_target_settings"][data-code="remove_duplicates"]').hide(),
		optionTarget = $('select[data-role="target"] option:selected', div),
		typeTargetFull = optionTarget.attr('data-type') ? optionTarget.attr('data-type') : '',
		typeTarget = typeTargetFull.split(':')[0],
		multipleTarget = optionTarget.attr('data-multiple') == 'Y';
	if(multipleTarget && typeTarget != 'G' && typeTarget != 'E' && typeTarget != 'F'){
		divField.show();
	}
	if(!divField.is(':visible')){
		$('input[type="checkbox"]', divField).prop('checked', false);
	}
}

function wdaToggleSettingsFileConvertName(){
	var
		divField = $('select[data-role="target"]', div).closest('.plugin-form__field')
			.children('div[data-role="group_target_settings"][data-code="file_convert_name"]').hide(),
		optionTarget = $('select[data-role="target"] option:selected', div),
		typeTargetFull = optionTarget.attr('data-type') ? optionTarget.attr('data-type') : '',
		typeTarget = typeTargetFull.split(':')[0];
	if(typeTarget == 'F'){
		divField.show();
	}
}

function wdaToggleSettingsCopyRawElementId(){
	var
		divField = $('select[data-role="target"]', div).closest('.plugin-form__field')
			.children('div[data-role="group_target_settings"][data-code="copy_raw_element_id"]').hide(),
		optionTarget = $('select[data-role="target"] option:selected', div),
		typeTargetFull = optionTarget.attr('data-type') ? optionTarget.attr('data-type') : '',
		typeTarget = typeTargetFull.split(':')[0];
	if(typeTarget == 'E'){
		divField.show();
	}
}

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="source"]', div).val()){
		return $('input[data-role="error_no_source_field"]', div).val();
	}
	if(!$('select[data-role="target"]', div).val()){
		return $('input[data-role="error_no_target_field"]', div).val();
	}
	return true;
});
