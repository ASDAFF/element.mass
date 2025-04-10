<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\Filter;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$bIncludeSubsections = !!$arParams['INCLUDE_SUBSECTIONS'];

// Show filter
$obFilter = new Filter($arParams['IBLOCK_ID'], $arParams['ENTITY_TYPE']);
if(is_array($arParams['SECTIONS_ID'])){
	$obFilter->setSectionsId($arParams['SECTIONS_ID']);
}
$obFilter->setIncludeSubsections($bIncludeSubsections);
$obFilter->setInputName('filter');
if(isset($arParams['FILTER_DATA'])){
	$obFilter->setJson($arParams['FILTER_DATA']);
}
if(Helper::getOption('default_logic_is_and') == 'Y'){
	$obFilter->setDefaultLogicAnd(true);
}
$obFilter->show();
$GLOBALS['WDA_FILTER_PRINT'] = $obFilter->buildFilter();
$obFilter = null;
?>