<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Filter,
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strLang = 'WDA_POPUP_PREVIEW_';

$obFilter = new Filter($arParams['IBLOCK_ID'], $arParams['ENTITY_TYPE']);
if(is_array($arParams['SECTIONS_ID'])){
	$obFilter->setSectionsId($arParams['SECTIONS_ID']);
}
$obFilter->setIncludeSubsections($arParams['INCLUDE_SUBSECTIONS']);
if(strlen($arParams['FILTER'])){
	$obFilter->setJson($arParams['FILTER']);
}
$arFilter = $obFilter->buildFilter();

if(!\Bitrix\Main\Loader::includeModule('iblock')){
	print Helper::getMessage('WDA_NO_IBLOCK_MODULE');
	return;
}

$arItems = [];
$intAllCount = 0;
$intMaxCount = 100;

$arSort = ['ID' => 'ASC'];
$arNavParams = ['nTopCount' => $intMaxCount];
$arSelect = ['ID', 'IBLOCK_ID', 'IBLOCK_TYPE_ID', 'IBLOCK_SECTION_ID', 'NAME', 'ACTIVE', 'CODE', 'XML_ID'];
if($arParams['ENTITY_TYPE'] == Helper::TYPE_ELEMENT) {
	$intAllCount = intVal(\CIBlockElement::getList($arSort, $arFilter, []));
	$resItems = \CIBlockElement::getList($arSort, $arFilter, false, $arNavParams, $arSelect);
	while($arItem = $resItems->getNext()){
		$arItems[] = $arItem;
	}
}
elseif($arParams['ENTITY_TYPE'] == Helper::TYPE_SECTION){
	$intAllCount = IBlock::getSectionCount($arFilter);
	$resItems = IBlock::getSectionList($arSort, $arFilter, $arSelect, $intMaxCount);
	if($resItems){
		while($arItem = $resItems->fetch()){
			$arItems[] = $arItem;
		}
	}
}
?>

<div class="wda-filtered-elements adm-list-table-wrap adm-list-table-without-header adm-list-table-without-footer">
	<table class="adm-list-table">
		<thead>
			<tr class="adm-list-table-header">
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
					<?=Helper::getMessage($strLang.'COL_ICON');?>
				</div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
					<?=Helper::getMessage($strLang.'COL_ID');?>
				</div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
					<?=Helper::getMessage($strLang.'COL_ACTIVE');?>
				</div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
					<?=Helper::getMessage($strLang.'COL_NAME');?>
				</div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
					<?=Helper::getMessage($strLang.'COL_CODE');?>
				</div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
					<?=Helper::getMessage($strLang.'COL_EXTERNAL_ID');?>
				</div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">
					<?=Helper::getMessage($strLang.'COL_SECTION_ID');?>
				</div></td>
			</tr>
		</thead>
		<tbody>
			<?foreach($arItems as $arItem):?>
				<?
				$strEntityIcon = '';
				if($arParams['ENTITY_TYPE'] == Helper::TYPE_ELEMENT) {
					$strUrl = sprintf('/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=%d&type=%s&ID=%d&lang=%s&'
						.'find_section_section=%d&WF=Y', $arItem['IBLOCK_ID'], $arItem['IBLOCK_TYPE_ID'], $arItem['ID'],
						LANGUAGE_ID, $arItem['IBLOCK_SECTION_ID']);
					$strEntityIcon = 'wda-icon-element';
				}
				elseif($arParams['ENTITY_TYPE'] == Helper::TYPE_SECTION){
					$strUrl = sprintf('/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=%d&type=%s&ID=%d&lang=%s&'
						.'find_section_section=%d', $arItem['IBLOCK_ID'], $arItem['IBLOCK_TYPE_ID'], $arItem['ID'],
						LANGUAGE_ID, $arItem['ID']);
					$strEntityIcon = 'wda-icon-section';
				}
				?>
				<tr class="adm-list-table-row">
					<td class="adm-list-table-cell align-left valign-top wda-filtered-elements__entity">
						<span class="<?=$strEntityIcon;?>"></span>
					</td>
					<td class="adm-list-table-cell align-left valign-top">
						<?=$arItem['ID'];?>
					</td>
					<td class="adm-list-table-cell align-left valign-top">
						<?=Helper::getMessage('MAIN_'.($arItem['ACTIVE'] == 'Y' ? 'YES' : 'NO'));?>
					</td>
					<td class="adm-list-table-cell align-left valign-top">
						<a href="<?=$strUrl;?>" target="_blank" class="wda-filtered-elements__link">
							<?=$arItem['NAME'];?>
							<span class="wda-filtered-elements__link-image wda-icon-blank"></span>
						</a>
					</td>
					<td class="adm-list-table-cell align-left valign-top">
						<?=$arItem['CODE'];?>
					</td>
					<td class="adm-list-table-cell align-left valign-top">
						<?=$arItem['XML_ID'];?>
					</td>
					<td class="adm-list-table-cell align-left valign-top">
						<?=$arItem['IBLOCK_SECTION_ID'];?>
					</td>
				</tr>
			<?endforeach?>
			<?if(empty($arItems)):?>
				<tr class="adm-list-table-row"><td colspan="6" class="adm-list-table-cell align-left"><?
					print Helper::getMessage($strLang.'EMPTY');
				?></td></tr>
			<?endif?>
		</tbody>
	</table>
</div>

<div data-role="preview-summary" style="visibility:hidden; height:0; overflow:hidden">
	<?if($intMaxCount < $intAllCount):?>
		<?=Helper::getMessage($strLang.'SUMMARY_PART', ['#SHOWN#' => $intMaxCount, '#ALL#' => $intAllCount]);?>
	<?else:?>
		<?=Helper::getMessage($strLang.'SUMMARY_FULL', ['#SHOWN#' => $intMaxCount, '#ALL#' => $intAllCount]);?>
	<?endif?>
</div>
