
// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	if(!$('select[data-role="source_field"]', div).val()){
		return $('input[data-role="error_no_source_field"]', div).val();
	}
	if(!$('select[data-role="cut_field"]', div).val()){
		return $('input[data-role="error_no_cut_field"]', div).val();
	}
	return true;
});