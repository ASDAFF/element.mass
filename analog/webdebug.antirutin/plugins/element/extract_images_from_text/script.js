/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){

	if(!$('[data-role="html_field"]', div).val()) {
		return $('input[data-role="error_no_html_field"]', div).val();
	}

	return true;
});
