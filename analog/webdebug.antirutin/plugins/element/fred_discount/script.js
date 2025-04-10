/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

wdaOnStartHandler(id, function(id, div, title){
	let inputNoModule = $('input[data-role="wda_fred_discount_error"]');
	if(inputNoModule.length){
		return false;
	}
	return true;
});
