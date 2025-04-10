<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

$strInputId = Helper::randString(true);

?>
<div data-role="user_select" data-id="<?=$strInputId;?>">
	<input type="text" name="<?=$this->getInputName('value', $bMultiple);?>" value="<?=$mValue;?>" size="6" data-role="user_select_value" onchange="SUVx2(this);" />
	<input type="button" onclick="window.SUVw1 = window.open('/bitrix/admin/user_search.php?lang=<?=LANGUAGE_ID;?>&JSFUNC=x1', '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));" value="..." />
	<span data-role="user_select_title" data-clear="Y"><?if($mValue>0){print Helper::getUserTitle($mValue, $intMode=4);}?></span>
</div>
<script>
window.SUVmode = 4;
window.SUVx1 = function(id){
	var div = $('div[data-role="user_select"][data-id="<?=$strInputId;?>"]');
	$('input[type="text"][data-role="user_select_value"]', div).val(id);
	$('span[data-role="user_select_title"]', div).text(id);
	wdaGetUserTitle(id, $('span[data-role="user_select_title"]', div), window.SUVmode);
	window.SUVw1.close();
}
window.SUVx2 = function(input){
	var id = parseInt($.trim($(input).val()));
	if(!isNaN(id)){
		wdaGetUserTitle(id, $(input).parent().find('span'), window.SUVmode);
	}
}
</script>