/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('textarea[data-role="code"]', div).val()){
		return $('input[data-role="error_no_code"]', div).val();
	}
	return true;
});
