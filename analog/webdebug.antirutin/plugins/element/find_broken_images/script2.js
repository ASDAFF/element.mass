/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

$(document).delegate(hid+' input[data-role="delete_broken_images"]', 'change', function(e){
	var
		divResetTmpId = $('input[data-role="reset_tmp_id"]', div).closest('.plugin-form__field');
	if($(this).is(':checked')){
		divResetTmpId.show();
	}
	else{
		divResetTmpId.hide();
	}
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('input[data-role="delete_broken_images"]', div).trigger('change');
});
