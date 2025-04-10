<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

$strInputId = Helper::randString(true);

$intLinkIBlockId = intVal($arField['DATA']['LINK_IBLOCK_ID']);

if($intLinkIBlockId > 0) {
	$strTableId = sprintf('iblockprop-E-%d-%d', $arField['DATA']['ID'], $intLinkIBlockId);
}
else{
	$intLinkIBlockId = '';
	$strTableId = sprintf('iblockprop-E-%d', $arField['DATA']['ID']);
}

$strK = 'n0';
$strIBlockFix = 'y';
$intWidth = 1000;
$intHeight = 600;
$strUrl = sprintf('/bitrix/admin/iblock_element_search.php?lang=%s&IBLOCK_ID=%d&n=%s&k=%s&iblockfix=%s&tableId=%s',
	LANGUAGE_ID, $intLinkIBlockId, 'e'.$strInputId, $strK, $strIBlockFix, $strTableId);
$strUrl = sprintf('jsUtils.OpenWindow(\'%s\', %d, %d);', $strUrl, $intWidth, $intHeight);

$mValue = intVal($mValue);
$strName = '';
if($mValue <= 0){
	$mValue = '';
}
elseif(\Bitrix\Main\Loader::includeModule('iblock')){
	$arFilter = [];
	$arFilter['ID'] = $mValue;
	if($intLinkIBlockId){
		$arFilter['IBLOCK_ID'] = $intLinkIBlockId;
	}
	$resElement = \CIBlockElement::getList([], $arFilter, false, false, ['NAME']);
	if($arElement = $resElement->getNext()){
		$strName = $arElement['NAME'];
	}
	else{
		$mValue = '';
	}
}

?>
<span>
	<input name="<?=$this->getInputName('value_'.$strInputId);?>" id="e<?=$strInputId;?>" value="<?=$mValue;?>"
		size="6" type="text" data-role="section_link_value" />
	<input type="button" value="..." onclick="<?=$strUrl;?>"
		>&nbsp;<span id="sp_e<?=$strInputId;?>" data-role="section_link_title" data-clear="Y"><?=$strName;?></span>
</span>
