<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strLang = 'WDA_POPUP_FILL_LIST_FROM_TEXT_';

?>
<style>
textarea[data-role="fill_list_from_text"]{
	box-sizing:border-box;
	height:100%;
	outline:none;
	resize:none;
	width:100%;
}
</style>
<div class="wda-form">
	<textarea data-role="fill_list_from_text" spellcheck="false" 
		placeholder="<?=Helper::getMessage($strLang.'FIELD_TEXT_PLACEHOLDER');?>"></textarea>
</div>
