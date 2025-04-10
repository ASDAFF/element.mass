// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="price_type"]', div).val()){
		return $('input[data-role="error_no_source_price"]', div).val();
	}
	return true;
});