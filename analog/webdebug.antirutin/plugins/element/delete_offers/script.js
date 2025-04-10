/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!confirm($('input[data-role="prompt_delete"]', div).val())){
		return $('input[data-role="prompt_cancelled"]', div).val();
	}
	return true;
});
