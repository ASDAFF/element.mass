/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Plugin load handler
/*
$(document).delegate(hid, 'pluginload', function(e){
	//
});
*/

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="field"]', div).val()){
		return $('input[data-role="error_no_field"]', div).val();
	}
	var sort = parseInt($('input[data-role="sort"]', div).val());
	if(isNaN(sort) || sort <= 0){
		return $('input[data-role="error_no_sort"]', div).val();
	}
	return true;
});
