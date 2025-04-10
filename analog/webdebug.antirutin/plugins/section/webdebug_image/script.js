/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if($('input[data-role="no_module"]', div).length){
		return $('input[data-role="error_no_module"]', div).val();
	}
	if(!$('select[data-role="source"]', div).val()){
		return $('input[data-role="error_no_source"]', div).val();
	}
	if(!$('select[data-role="target"]', div).val()){
		return $('input[data-role="error_no_target"]', div).val();
	}
	if(!$('select[data-role="profile"]', div).val()){
		return $('input[data-role="error_no_profile"]', div).val();
	}
	return true;
});
