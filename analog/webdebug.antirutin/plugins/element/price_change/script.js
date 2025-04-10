/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="source"]', 'change', function(e){
	var
		value = $(this).val(),
		type = $('option:selected', this).attr('data-type'),
		divLimit = $('input[data-role="limit"]', div).closest('.plugin-form__field'),
		divSourceCurrency = $('select[data-role="source_currency"]', div).closest('.plugin-form__field');
	wdaPriceChangeHandler();
	if(value == 'CATALOG_PURCHASING_PRICE') {
		divLimit.hide();
	}
	else{
		divLimit.show();
	}
	if(value.match(/^PROPERTY_/)) {
		divSourceCurrency.show();
	}
	else{
		divSourceCurrency.hide();
	}
});

$(document).delegate(hid+' select[data-role="target"]', 'change', function(e){
	wdaPriceChangeHandler();
});

function wdaPriceChangeHandler(){
	var
		selectSource = $('select[data-role="source"]', div),
		selectTarget = $('select[data-role="target"]', div),
		source = selectSource.val(),
		target = selectTarget.val(),
		divOffers = $('input[data-role="offers"]', div).closest('div.plugin-form__field').hide();
	if(wdaPriceChangeIsCatalogPrice(source) && wdaPriceChangeIsCatalogPrice(target)){
		divOffers.show();
	}
}

function wdaPriceChangeIsCatalogPrice(price){
	return price == 'CATALOG_PURCHASING_PRICE' || price.match(/^CATALOG_PRICE_(\d+)$/);
}

$(document).delegate(hid+' input[data-role="use_discounts"]', 'change', function(e){
	var
		select = $(this).closest('label').next('div[data-role]').hide();
	if($(this).is(':checked')){
		select.show();
	}
});

$(document).delegate(hid+' input[data-role="change_currency"]', 'change', function(e){
	var
		select = $(this).closest('label').next('div[data-role]').hide();
	if($(this).is(':checked')){
		select.show();
	}
});

$(document).delegate(hid+' input[data-role="change_price"]', 'change', function(e){
	var
		select = $(this).closest('label').next('div[data-role]').hide();
	if($(this).is(':checked')){
		select.show();
	}
});

$(document).delegate(hid+' input[data-role="change_price_add_row"]', 'click', function(e){
	var
		table = $(this).parent().find('table').first(),
		tbody = table.children('tbody').first(),
		rowFirst = tbody.children('tr').first(),
		rowNew = rowFirst.clone();
	rowNew.find('input[type="text"]').val('');
	rowNew.appendTo(tbody);
});

$(document).delegate(hid+' td[data-role="change_price_delete"] input[type="button"]', 'click', function(e){
	var
		row = $(this).closest('tr');
	row.remove();
});

$(document).delegate(hid+' input[data-role="round"]', 'change', function(e){
	var
		select = $(this).closest('label').next('div[data-role]').hide();
	if($(this).is(':checked')){
		select.show();
	}
});

$(document).delegate(hid+' input[data-role="limit"]', 'change', function(e){
	var
		select = $(this).closest('label').next('div[data-role]').hide();
	if($(this).is(':checked')){
		select.show();
	}
});

$(document).delegate(hid+' input[data-role="format"]', 'change', function(e){
	var
		select = $(this).closest('label').next('div[data-role]').hide();
	if($(this).is(':checked')){
		select.show();
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('select[data-role="source"]', div).trigger('change');
	$('select[data-role="target"]', div).trigger('change');
	$('input[data-role="use_discounts"]', div).trigger('change');
	$('input[data-role="change_currency"]', div).trigger('change');
	$('input[data-role="change_price"]', div).trigger('change');
	$('input[data-role="round"]', div).trigger('change');
	$('input[data-role="limit"]', div).trigger('change');
	$('input[data-role="format"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="source"] option:not([value=""]):selected', div).length){
		return $('input[data-role="error_no_source"]', div).val();
	}
	if(!$('select[data-role="target"] option:not([value=""]):selected', div).length){
		return $('input[data-role="error_no_target"]', div).val();
	}
	return true;
});
