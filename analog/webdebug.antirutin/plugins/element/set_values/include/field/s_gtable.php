<?
namespace WD\Antirutin;
$bAllowMultiply = false;
$arProperty = &$arField['DATA'];
parse_str($mValue, $arSavedValue);
if(is_array($arSavedValue) && is_array($arSavedValue['PROP'][$arProperty['ID']])){
	$mValue = $arSavedValue['PROP'][$arProperty['ID']];
	unset($mValue['GTEMPTY']);
}
else{
	$mValue = [];
}
$arProperty['VALUE'] = $mValue;
$arProperty['~VALUE'] = $mValue;
?>
<div data-role="wda_gtable_filter_value_wrapper" id="<?=$this->getId();?>_s_gtable">
	<input type="hidden" name="<?=$this->getInputName('value');?>" value=""
		data-role="wda_gtable_filter_value_hidden" />
	<?
	ob_start();
	_ShowUserPropertyField('PROP['.$arProperty['ID'].']', $arProperty, $mValue, true, false, 50000, '', false);
	$strHtml = preg_replace('#<tr>#', '<tr data-role="wda_gtable_filter_value" id="tr_PROPERTY_'.$arProperty['ID'].'">', 
		ob_get_clean(), 1);
	$strHtml = preg_replace('#<select\s#i', '<select data-no-style="Y" ', $strHtml);
	print $strHtml;
	?>
	<br/>
	<?=Helper::showNote(static::getMessage('FILE_NOT_SUPPORTED'), true);?>
	<br/>
</div>
<script>
if(!window.wdaSetValuesGtableFilterInterval_<?=$this->getId();?>_s_gtable){
	window.wdaSetValuesGtableFilterInterval_<?=$this->getId();?>_s_gtable = setInterval(function(){
		$('tr[data-role="wda_gtable_filter_value"]').each(function(){
			var div = $(this).closest('div[data-role="wda_gtable_filter_value_wrapper"]'),
				inputWd = $('input[data-role="wda_gtable_filter_value_hidden"]', div),
				gtableFormData = $('tr[data-role="wda_gtable_filter_value"] :input').serialize();
			inputWd.val(gtableFormData);
		});
	}, 1000);
}
</script>
