<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

$bAllowMultiply = false;

#
$strInputId = Helper::randString(true);

#
$strRequestMethod = $_SERVER['REQUEST_METHOD'];
$_SERVER['REQUEST_METHOD'] = 'GET';

#
$intRowCount = 8;

#ToDo: в свойстве указано по умолчанию HTML, но тут это не учитывается. Нужно доделать.

$strHeight = $arField['DATA']['USER_TYPE_SETTINGS']['height'];
if(is_numeric($strHeight)){
	$strHeight = sprintf('height:%dpx', $strHeight);
}

#
if($bAjax){
	$strType = 'text';
	if(in_array($strField, ['PREVIEW_TEXT', 'DETAIL_TEXT'])){
		$arIBlockFields = \CIBlock::getFields($this->intIBlockId);
		$strType = toLower($arIBlockFields[$strField.'_TYPE']['DEFAULT_VALUE']);
		$mValue = $arIBlockFields[$strField]['DEFAULT_VALUE'];
	}
	elseif($intPropertyId = IBlock::isProperty($strField)){
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			$arProperty = \CIBlockProperty::getById($intPropertyId, $this->intIBlockId)->getNext(false, false);
			if(is_array($arProperty['DEFAULT_VALUE']) && isset($arProperty['DEFAULT_VALUE']['TYPE'])){
				$strType = toLower($arProperty['DEFAULT_VALUE']['TYPE']);
				$mValue = $arProperty['DEFAULT_VALUE']['TEXT'];
			}
		}
	}
}

if(is_array($this->arSavedData['type'])){
	$strType = $this->arSavedData['type'][$intValueKey];
}
elseif(strlen($this->arSavedData['type'])){
	$strType = $this->arSavedData['type'];
}
else{
	$strType = $arField['DATA']['DEFAULT_VALUE']['TYPE'] == 'HTML' ? 'html' : 'text';
}

$arEditorJs = [
	'/bitrix/js/fileman/html_editor/range.js',
	'/bitrix/js/fileman/html_editor/html-actions.js',
	'/bitrix/js/fileman/html_editor/html-views.js',
	'/bitrix/js/fileman/html_editor/html-parser.js',
	'/bitrix/js/fileman/html_editor/html-base-controls.js',
	'/bitrix/js/fileman/html_editor/html-controls.js',
	'/bitrix/js/fileman/html_editor/html-components.js',
	'/bitrix/js/fileman/html_editor/html-snippets.js',
	'/bitrix/js/fileman/html_editor/html-editor.js',
	'/bitrix/modules/fileman/install/js/fileman/comp_params_manager/component_params_manager.js',
];
$arEditorCss = [
	'/bitrix/js/fileman/html_editor/html-editor.css',
];
?>

<style>
div[data-role="html_field_wrapper"] .bx-html-editor {margin-bottom:3px;}
</style>
<div data-role="html_field_wrapper" style="position:relative;">
	<?foreach($arEditorJs as $strEditorJs):?>
		<script src="<?=$strEditorJs;?>?<?=filemtime($_SERVER['DOCUMENT_ROOT'].$strEditorJs);?>"></script>
	<?endforeach?>
	<?foreach($arEditorCss as $strEditorCss):?>
		<link rel="stylesheet" href="<?=$strEditorCss;?>?<?=filemtime($_SERVER['DOCUMENT_ROOT'].$strEditorCss);?>"></link>
	<?endforeach?>
	<?
	ob_start();
	\CFileMan::showTypeSelector([
		'name' => $strInputId,
		'key' => NULL,
		'strTextTypeFieldName' => $this->getInputName('type_'.$strInputId, $bMultipleTmp=false),
		'strTextTypeValue' => $strType,
		'bSave' => true,
	]);
	$strHtmlType = ob_get_clean();
	//
	ob_start();
	$arHtmlEditControlParams = [
		'bUseOnlyDefinedStyles' => true,
		'bFromTextarea' => true,
		'bDisplay' => false,
		'bWithoutPHP' => true,
		'arTaskbars' => [
			'BXPropertiesTaskbar',
			'BXSnippetsTaskbar',
		],
		'height' => 450,
		'site' => LANGUAGE_ID,
		'arAdditionalParams' => [],
		'setFocusAfterShow' => false,
	];
	\CFileman::showHtmlEditControl($strInputId, $mValue, $arHtmlEditControlParams);
	$strHtmlText = ob_get_clean();
	//
	print $strHtmlType;
	print $strHtmlText;
	//?>
	<textarea name="<?=$this->getInputName('value_'.$strInputId, $bMultipleTmp=false);?>" id="bxed_<?=$strInputId;?>" 
		rows="<?=$intRowCount;?>" cols="<?=$intColCount;?>" data-role="html_field_textatea" wrap="virtual"
		style="box-sizing:border-box; <?=$strHeight?>; min-height:50px; resize:vertical; width:100%;"
		><?=$mValue;?></textarea>
</div>

<?$_SERVER['REQUEST_METHOD'] = $strRequestMethod;?>