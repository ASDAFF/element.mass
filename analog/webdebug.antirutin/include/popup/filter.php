<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Filter,
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

# Request Query
list($arGet, $arPost) = Helper::getRequestQuery();

# Post data
$strCurrentField = $arPost['current_field'];
$strCurrentLogic = $arPost['current_logic'];
$strCurrentValue = $arPost['current_value'];
$strCurrentValueTitle = $arPost['current_value_title'];
$bOffers = $arPost['iblock_type'] == 'offers' ? true : false;

#
$arCurrentValue = explode(Filter::VALUE_SEPARATOR, $strCurrentValue);
foreach($arCurrentValue as $key => $value){
	if(is_string($value) && !strlen($value)) {
		unset($arCurrentValue[$key]);
	}
}

# Include specified popup
$strFilterEntity = $arPost['entity'];
switch($strFilterEntity){
	case 'field':
		$strFile = __DIR__.'/filter_field.php';
		Helper::loadMessages($strFile);
		require($strFile);
		break;
	case 'logic':
		$strFile = __DIR__.'/filter_logic.php';
		Helper::loadMessages($strFile);
		require($strFile);
		break;
	case 'value':
		$strFile = __DIR__.'/filter_value.php';
		Helper::loadMessages($strFile);
		require($strFile);
		break;
}
?>