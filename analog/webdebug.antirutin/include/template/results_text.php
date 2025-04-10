<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\ProfileTable as Profile;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

?>