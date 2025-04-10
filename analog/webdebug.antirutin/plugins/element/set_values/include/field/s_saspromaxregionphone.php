<?
namespace WD\Antirutin;
$bAllowMultiply = false;
$arProperty = &$arField['DATA'];
$mValue = is_array($mValue) ? $mValue : [$mValue];
$arProperty['VALUE'] = $mValue;
$arProperty['~VALUE'] = $mValue;
?>
<div data-role="wda_aspro_max_phones_value_wrapper">
	<input type="hidden" name="<?=$this->getInputName('value');?>" value="<?=htmlspecialcharsbx($mValue);?>"
		data-role="wda_aspro_max_phones_value_hidden" />
	<?
	ob_start();
	_ShowUserPropertyField('PROP['.$arProperty['ID'].']', $arProperty, $mValue, true, false, 50000, '', false);
	$strHtml = ob_get_clean();
	// $strHtml = preg_replace('#<tr>#', '<tr data-role="wda_aspro_max_phones_value" id="tr_PROPERTY_'.$arProperty['ID'].'">', $strHtml, 1);
	print $strHtml;
	?>
</div>
<script>
(function(){
	let
		tableId = 'tb<?=md5(htmlspecialcharsbx('PROP['.$arProperty['ID'].']'))?>',
		inputWd = $('input[name="<?=$this->getInputName('value');?>"]'),
		phones, json;
	if(!window.wdaSetValuesAsproMaxPhonesInterval){
		window.wdaSetValuesAsproMaxPhonesInterval = setInterval(function(){
			phones = [];
			$('.aspro_property_regionphone_item').each(function(){
				let inputs = $(':input', this);
				phones.push({
					PHONE: inputs.eq(0).val(),
					HREF: inputs.eq(1).val(),
					ICON: inputs.eq(2).val(),
					DESCRIPTION: inputs.eq(3).val(),
				});
			});
			json = JSON.stringify(phones);
			if(inputWd.val() != json){
				inputWd.val(json);
			}
		}, 1000);
	}
	new JRegionPhone(tableId);
})();
</script>
<style>
#<?=$this->strId;?> input[data-role="field_input_description"],
#<?=$this->strId;?> table[data-role="field_items"] ~ span {display:none!important;}
</style>