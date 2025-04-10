/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="field"]', div).val()){
		return $('input[data-role="error_no_field"]', div).val();
	}
	else if($('select[data-role="action"]', div).val() == 'delete'){
		if(!confirm($('input[data-role="prompt_delete"]', div).val())){
			return $('input[data-role="prompt_cancelled"]', div).val();
		}
	}
	return true;
});