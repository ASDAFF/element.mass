<?
namespace WD\Antirutin;

$bAllowMultiply = false;

$strInputNameValue = $this->getInputName(static::INPUT_VALUE, true);
$strInputNameDescr = $this->getInputName(static::INPUT_DESCRIPTION, true);
$strFiledropTitle = \WD\Antirutin\Helper::getMessage('WDA_FILEDROP_TITLE');

$arPictureFields = ['PICTURE', 'DETAIL_PICTURE'];
$bPictureIsField = in_array($strField, $arPictureFields);
$bWithDescription = $arField['DATA']['WITH_DESCRIPTION'] == 'Y' || $bPictureIsField;

$arMedialibTypes = [];
if($bPictureIsField){
	$arMedialibTypes = ['image'];
}

$strFileFilter = '';
if($bPictureIsField){
	$strFileFilter = 'jpg,jpeg,png,gif,webp';
}
elseif(is_array($arField['DATA']['SETTINGS']['EXTENSIONS'])){
	$strFileFilter = implode(',', array_keys($arField['DATA']['SETTINGS']['EXTENSIONS']));
}

$arFiles = [];
foreach($arValues as $intValueKey => $strValue){
	if(strlen(trim($strValue))){
		$arFile = [
			'name' => $strValue,
			'size' => filesize($_SERVER['DOCUMENT_ROOT'].$strValue),
			'type' => mime_content_type($_SERVER['DOCUMENT_ROOT'].$strValue),
			'file' => $_SERVER['DOCUMENT_ROOT'].$strValue,
		];
		if($bWithDescription){
			$arFile['description'] = $arDescriptions[$intValueKey];
		}
		$arFiles[] = $arFile;
	}
}
$strFilesJson = \Bitrix\Main\Web\Json::encode($arFiles);
?>
<style>
table[data-role="field_items"] {width:100%;}
</style>
<div data-role="wda_filedrop" data-field="<?=$strField;?>"></div>
<div class="wda_filedrop_bx_buttons">
	<?if(Helper::getOption('use_medialib', null, 'fileman') != 'N'):?>
		<?if(\CMedialib::canDoOperation('medialib_view_collection', 0)):?>
			<?=\CMedialib::showBrowseButton([
				'mode' => 'medialib',
				'value' => static::getMessage('SELECT_FILE_FROM_MEDIALIB'),
				'event' => 'WDA_'.$this->getId(),
				'id' => 'wda_btn_'.mb_strtolower($this->getId()),
				'MedialibConfig' => [
					'event' => 'bx_ml_event_'.$this->getId(),
					'arResultDest' => ['FUNCTION_NAME' => 'WDA_SET_VALUES_ONRESULT_MEDIALIB_'.$this->getId()],
					'types' => $arMedialibTypes,
				],
				'bReturnResult' => true
			]);?>
		<?endif?>
	<?endif?>
	<?if($GLOBALS['USER']->canDoOperation('fileman_view_file_structure')):?>
		<?=\CMedialib::showBrowseButton([
			'event' => 'WDA_SET_VALUES_'.$this->getId(),
			'mode' => 'file_dialog',
			'value' => static::getMessage('SELECT_FILE_FROM_STRUCTURE'),
			'id' => 'wda_btn_'.mb_strtolower($this->getId()),
			'bReturnResult' => true
		]);?>
		<?\CAdminFileDialog::showScript([
			'event' => 'WDA_SET_VALUES_'.$this->getId(),
			'arResultDest' => ['FUNCTION_NAME' => 'WDA_SET_VALUES_ONRESULT_FILE_DIALOG_'.$this->getId()],
			'arPath' => [],
			'select' => 'F',
			'operation' => 'O',
			'showUploadTab' => true,
			'showAddToMenuTab' => false,
			'fileFilter' => $strFileFilter,
			'allowAllFiles' => true,
			'SaveConfig' => true
		]);?>
	<?endif?>
</div>
<script>
(()=>{
	window.wdaSetValuesFileInput_<?=$this->getId();?> = $('#<?=$this->getId();?> [data-role="wda_filedrop"]');
	window.wdaSetValuesConfig = {
		ajaxUrl: location.href + (location.href.indexOf('?') == -1 ? '?' : '&') + 'ajax_action=file_upload',
		files: <?=$strFilesJson;?>,
		inputNameValue: '<?=$strInputNameValue;?>',
		inputNameDescr: '<?=$strInputNameDescr;?>',
		caption: '<?=$strFiledropTitle;?>',
		multiple: <?=($bMultiple ? 'true' : 'false');?>,
		withDescription: <?=($bWithDescription ? 'true' : 'false');?>
	};
	window.wdaSetValuesFileInput_<?=$this->getId();?>.fileDrop(window.wdaSetValuesConfig);
	//
	$('#wda_btn_<?=$this->getId();?>').removeAttr('style');
	window.WDA_SET_VALUES_ONRESULT_MEDIALIB_<?=$this->getId();?> = function(item){
		window.wdaSetValuesFileInput_<?=$this->getId();?>.trigger('addcustomfile', {name:item.src, fileObject:item.name});
	}
	window.WDA_SET_VALUES_ONRESULT_FILE_DIALOG_<?=$this->getId();?> = function(filename, path, site, title, menu){
		name = path + '/' + filename;
		name = name.replace('//', '/');
		window.wdaSetValuesFileInput_<?=$this->getId();?>.trigger('addcustomfile', {name:name, fileObject:filename});
	}
})();
</script>
<?=Helper::showNote(static::getMessage('MACROS_NOTE'), true);?>
<br/>
<?=Helper::showNote(static::getMessage('TMP_FILE_NOTE'), true);?>
<br/>
