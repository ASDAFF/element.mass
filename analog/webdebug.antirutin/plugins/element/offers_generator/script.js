/* See additional vars in sources: \WD\Antirutin\Plugin::printJs() */

// Check box 'iblock_element_update'
$(document).delegate(hid+' select[data-role="mode"]', 'change', function(e){
	let
		mode = $(this).val();
	$('div[data-role="offer_generator_modes_div"]').children(`[data-mode="${mode}"]`).show().siblings().hide();
});

// Plugin load handler
$(document).delegate(hid, 'pluginload', function(e){
	$('select[data-role="mode"]', div).trigger('change');
});

// Check fields on start
wdaOnStartHandler(id, function(id, div, title){
	let
		mode = $('select[data-role="mode"]', div).val(),
		simple_Count = parseInt($('input[data-role="count"]', div).val());
	switch(mode){
		case 'simple':
			if(isNaN(simple_Count) || simple_Count <= 0){
				return $('input[data-role="error_simple_no_count"]', div).val();
			}
			break;
		default:
			// alert('!!');
			break;
	}
	return true;
});
