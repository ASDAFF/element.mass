/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' select[data-role="format"]', 'change', function(e){
	// Change extension for filename
	var inputFilename = $('input[data-role="filename"]', div),
		filename = inputFilename.val(),
		extension = $(this).val().toLowerCase(),
		isCsv = extension == 'csv',
		fieldForExcel = $('select[data-role="for_excel"]', div).closest('.plugin-form__field'),
		fieldFields = $('select[data-role="fields_all"]', div).closest('.plugin-form__field');
	if(filename.match(/\.[a-z0_]*$/)){
		filename = filename.replace(/\.[a-z0_]*$/, '.' + extension);
	}
	else{
		filename += '.' + extension;
	}
	inputFilename.val(filename);
	// Fields
	if(isCsv){
		fieldForExcel.show();
		fieldFields.show();
	}
	else{
		fieldForExcel.hide();
		fieldFields.hide();
	}
});

$(document).delegate(hid+' div[data-role="fields_filter"] input', 'input', function(e){
	var search = $.trim($(this).val().toLowerCase());
		select = $(this).closest('td[data-role]').find('select[data-role]');
	select.find('option').each(function(){
		var text = $(this).val() + ' ' + $(this).text().toLowerCase();
		$(this).show();
		if(text.indexOf(search) == -1){
			$(this).hide();
		}
	});
	select.find('optgroup').each(function(){
		$(this).show();
		if(!$(this).find('option:visible').length){
			$(this).hide();
		}
	});
});

$(document).delegate(hid+' input[data-role="fields_move_right"]', 'click', function(e){
	var selectSource = $('select[data-role="fields_all"]', div),
		selectTarget = $('select[data-role="fields_selected"]', div),
		options = selectSource.find('option:selected');
	if(options.length){
		options.each(function(){
			if(!selectTarget.find('option[value="'+$(this).val()+'"]').length){
				selectTarget.append($(this).clone());
			}
		});
	}
	serializeValues();
});

$(document).delegate(hid+' input[data-role="fields_move_up"]', 'click', function(e){
	var options = $('select[data-role="fields_selected"] option:selected', div);
	options.first().prev().before(options);
	options.hide().show();
	serializeValues();
});

$(document).delegate(hid+' input[data-role="fields_move_down"]', 'click', function(e){
	var options = $('select[data-role="fields_selected"] option:selected', div);
	options.last().next().after(options);
	options.hide().show();
	serializeValues();
});

$(document).delegate(hid+' input[data-role="fields_delete"]', 'click', function(e){
	var options = $('select[data-role="fields_selected"] option:selected', div);
	options.remove();
	serializeValues();
});

function serializeValues(){
	var values = [];
	$('select[data-role="fields_selected"] option', div).each(function(){
		values.push($(this).val());
	});
	$('input[data-role="fields_serialized"]', div).val(values.join(','));
}

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('select[data-role="format"]', div).trigger('change');
	wdaSelect2($('select[data-role="format"]', div));
	wdaSelect2($('select[data-role="encoding"]', div));
	wdaSelect2($('select[data-role="for_excel"]', div));
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$.trim($('input[data-role="filename"]', div).val()).length){
		return $('input[data-role="error_no_filename"]', div).val();
	}
	else if($('select[data-role="format"]', div).val() == 'CSV'){
		if(!$('select[data-role="fields_selected"] option', div).length){
			return $('input[data-role="error_no_csv_fields"]', div).val();
		}
	}
	return true;
});
