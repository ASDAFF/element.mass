/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="function_name"]', 'change', function(e, data){
	var
		functionName = $(this).val(),
		jsonText = $('option:selected', this).attr('data-arguments'),
		jsonArguments = jsonText != undefined ? JSON.parse(jsonText) : {},
		divAttributes = $('div[data-role="function_arguments"]', div),
		initial = data != undefined && data.json  != undefined,
		jsonInitial = initial ? data.json : null,
		randId = null,
		pluginId = null;
		name = null;
	if(jsonInitial != null){
		jsonArguments = jsonInitial;
	}
	divAttributes.html('');
	$.each(jsonArguments, $.proxy(function(key, arg){
		if(!arg.IS_FIELD){
			if(!initial){
				arg.VALUE = arg.DEFAULT;
			}
			randId = 'id' + $.trim(Math.random()).substr(2);
			pluginId = $(this).closest('div[data-plugin-form]').attr('id');
			name = 'actions[' + pluginId + '][arg_' + key + ']';
			divAttributes.append(
				$('<div>')
					.append($('<div/>').html(arg.NAME)
						.append(arg.HINT != undefined ? '<span id="'+randId+'"><span></span></span>' : '')
					)
					.append($('<input/>').attr({type:'text', name:name, size:70, placeholder:arg.PLACEHOLDER})
						.val(arg.VALUE != undefined ? arg.VALUE : ''))
					.toggleClass('function_argument_required', arg.REQUIRED)
			);
			if(arg.HINT){
				BX.hint_replace($("#"+randId).children().get(0), arg.HINT);
			}
		}
	}, this));
	//console.log(json);
	
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	wdaSelect2($('select[data-role="function_name"]', div));
	$('select[data-role="function_name"]', div).each(function(){
		let json = $(this).attr('data-json');
		if(json != undefined){
			$(this).trigger('change', {json: JSON.parse(json)});
		}
	});
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="field"]', div).val()){
		return $('input[data-role="error_no_field"]', div).val();
	}
	else{
		/*
		var mode = $('select[data-role="mode"]', div).val();
		if(mode == 'simple' && !$('input[data-role="simple_search"]', div).val().length){
			return $('input[data-role="error_no_simple_search"]', div).val();
		}
		else if(mode == 'reg_exp' && !$('input[data-role="reg_exp_search"]', div).val().length){
			return $('input[data-role="error_no_reg_exp_search"]', div).val();
		}
		else if(mode == 'append' && !$('textarea[data-role="append_text"]', div).val().length){
			return $('input[data-role="error_no_append_text"]', div).val();
		}
		else if(mode == 'prepend' && !$('textarea[data-role="prepend_text"]', div).val().length){
			return $('input[data-role="error_no_prepend_text"]', div).val();
		}
		*/
	}
	return true;
});
