/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).on('click', hid+' input[data-role="product_select"]', function(e){
	let
		btnSelect = $(this),
		inputValue = btnSelect.parent().find('input[type="text"]'),
		divCaption = btnSelect.parent().next(),
		funcName = 'SelectProductPopupCallback_' + id,
		popup = new BX.CDialog({
			content_url: '/bitrix/admin/cat_product_search_dialog.php?lang='+phpVars.LANGUAGE_ID+'&func_name='+funcName,
			height: window.innerHeight - 100,
			width: window.innerWidth - 100,
			draggable: true,
			resizable: true,
			min_height: 500,
			min_width: 800,
		});
	window[funcName] = function(product, iblockId){
		console.log(product, iblockId, btnSelect, inputValue, divCaption);
		inputValue.val(product.id);
		divCaption.html(product.name);
		popup.Close();
	}
	BX.addCustomEvent(popup, 'onWindowRegister', BX.defer(function(){
		popup.Get().style.position = 'fixed';
		popup.Get().style.top = (parseInt(popup.Get().style.top) - BX.GetWindowScrollPos().scrollTop) + 'px';
	}));
	popup.Show();
});

$(document).on('click', hid+' input[data-role="row_delete"]', function(e){
	$(this).closest('tr').fadeOut(function(){
		$(this).remove();
	});
});

$(document).on('click', hid+' input[data-role="row_add"]', function(e){
	let
		body = $('.wda_product_set-table_wrapper > table > tbody', div),
		row = body.children('tr').first(),
		newRow = row.clone();
	$('input[type="text"]', newRow).each(function(){
		$(this).val($.trim($(this).attr('data-default')));
		$(this).attr('placeholder', $.trim($(this).attr('data-default')));
	});
	$('.wda_product_set-product_name', newRow).html('');
	newRow.appendTo(body);
});

$(document).on('pluginload', hid, function(e){
	if($('.wda_product_set-table_wrapper > table > tbody > tr', div).length <= 1){
		$('input[data-role="row_add"]', div).trigger('click');
	}
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	// if(!confirm($('input[data-role="prompt_delete"]', div).val())){
	// 	return $('input[data-role="prompt_cancelled"]', div).val();
	// }
	return true;
});
