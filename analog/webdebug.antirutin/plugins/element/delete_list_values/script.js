/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="field"]', 'change', function(e){
	e.preventDefault();
	var field = $(this).val(),
		data,
		selectValue = $('select[data-role="value"]', div).html(''),
		divValue = selectValue.closest('.plugin-form__field').hide();
	if(field.length){
		BX.showWait();
		data = {
			plugin: getPlugin(id),
			plugin_id: id,
			field: field
		};
		divValue.show();
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

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="field"]', div).val()){
		return $('input[data-role="error_no_field"]', div).val();
	}
	else if(!$('select[data-role="value"] option:not([value=""]):selected', div).length){
		return $('input[data-role="error_no_value"]', div).val();
	}
	return true;
});
