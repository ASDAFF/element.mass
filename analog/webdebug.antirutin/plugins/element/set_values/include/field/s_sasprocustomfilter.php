<?
namespace WD\Antirutin;
$arProperty = &$arField['DATA'];
$mValue = is_array($mValue) ? $mValue : [$mValue];
$arProperty['VALUE'] = $mValue;
$arProperty['~VALUE'] = $mValue;
?>
<div data-role="wda_aspro_filter_value_wrapper">
	<input type="hidden" name="<?=$this->getInputName('value');?>" value="<?=htmlspecialcharsbx($mValue);?>"
		data-role="wda_aspro_filter_value_hidden" />
	<?
	ob_start();
	_ShowUserPropertyField('PROP['.$arProperty['ID'].']', $arProperty, $mValue, true, false, 50000, '', false);
	$strHtml = preg_replace('#<tr>#', '<tr data-role="wda_aspro_filter_value" id="tr_PROPERTY_'.$arProperty['ID'].'">', 
		ob_get_clean(), 1);
	print $strHtml;
	?>
</div>
<script>
if(!window.wdaSetValuesAsproFilterInterval){
	window.wdaSetValuesAsproFilterInterval = setInterval(function(){
		$('tr[data-role="wda_aspro_filter_value"]').each(function(){
			var div = $(this).closest('div[data-role="wda_aspro_filter_value_wrapper"]'),
				inputWd = $('input[data-role="wda_aspro_filter_value_hidden"]', div);
				inputAspro = $(this).children('td').children('input[type="hidden"]').first(),
				inputAsproValue = inputAspro.val();
			if(inputWd.val() != inputAsproValue) {
				inputWd.val(inputAsproValue);
			}
		});
	}, 1000);
}
</script>
