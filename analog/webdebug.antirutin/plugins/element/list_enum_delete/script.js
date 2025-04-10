/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	// Handlers
	$('input[data-role="select_delete_enums"]', div).trigger('change');
});

// Show enums on ajax
$(document).delegate(hid+' select[data-role="field"]', 'change', function(e){
	e.preventDefault();
	var field = $(this).val(),
		data,
		selectValue = $('select[data-role="value"]', div).html('');
	if(field.length){
		BX.showWait();
		data = {
			plugin: getPlugin(id),
			plugin_id: id,
			field: field
		};
		wdaAjax(['custom_ajax', 'load_select_value'], data, function(jqXHR, textStatus, arJson){
			if(arJson.Success){
				selectValue.html(arJson.Html);
			}
			else{
				wdaPopupError.Open(jqXHR);
			}
			BX.closeWait();
		}, function(jqXHR, textStatus, errorThrown){
			wdaPopupError.Open(jqXHR);
			BX.closeWait();
		});
	}
});

// Checkbox clarify
$(document).delegate(hid+' input[data-role="select_delete_enums"]', 'change', function(e){
	$('div[data-role="delete_enums_wrapper"]', div).toggle($(this).prop('checked'));
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	return true;
});
