/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' input[data-role="filter"]', 'input', function(e){
	var
		text = $.trim($(this).val().toLowerCase()),
		filtered = !!text.length,
		select = $(this).closest('[data-role="select_section"]').find('select'),
		options = select.find('option').not('[value=""]');//.removeAttr('selected');
	if(filtered){
		options.hide().each(function(){
			$(this).text($(this).attr('data-text-full'));
			if(($(this).val() + $(this).text()).toLowerCase().indexOf(text) != -1){
				$(this).show();
			}
		});
	}
	else{
		options.show().each(function(){
			$(this).text($(this).attr('data-text-lite'));
		});
	}
});

// Handle change action
$(document).delegate(hid+' select[data-role="action"]', 'change', function(e){
	let	
		selectSections = $('select[data-role="section"]', div),
		selectSectionsName = selectSections.attr('name').replace(/\[\]$/, '');
	if($(this).val() == 'add' || $(this).val() == 'delete'){
		selectSectionsName += '[]';
		selectSections.attr('multiple', 'multiple');
	}
	else{
		selectSections.removeAttr('multiple');
	}
	if(selectSectionsName != selectSections.attr('name')){
		selectSections.attr('name', selectSectionsName);
	}
	$('.plugin-form__field[data-role="selected_section"]').toggle($(this).val() != 'correct');
});

// Handle select sections
$(document).delegate(hid+' select[data-role="section"]', 'change', function(e){
	let value = $(this).val();
	if(value){
		value = typeof $(this).val() == 'object' ? value : [value];
		$('span[data-role="link_sections_count"]', div).text(value.length);
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('select[data-role="action"]', div).trigger('change');
	$('select[data-role="section"]', div).trigger('change');
	wdaSelect2($('select[data-role="action"]', div));
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="action"]', div).val()){
		return $('input[data-role="error_no_action"]', div).val();
	}
	let
		action = $('select[data-role="action"]', div).val();
	if(!$('select[data-role="section"]', div).val() && action != 'set' && action != 'correct'){
		return $('input[data-role="error_no_section"]', div).val();
	}
	return true;
});
