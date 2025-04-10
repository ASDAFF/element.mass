/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('input[data-role="wget_site_homepage"]', div).val().length){
		return $('input[data-role="empty_site_homepage"]', div).val();;
	}
	return true;
});
