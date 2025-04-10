<?php
namespace WD\Antirutin;
$bAllowMultiply = false;
$arProperty = &$arField['DATA'];
$mValue = array_map(function($item){
	return ['VALUE' => $item, 'DESCRIPTION' => ''];
}, array_filter($arValues));
$arProperty['VALUE'] = $arProperty['~VALUE'] = $mValue;
$inputName = $this->getInputName('value', true);
?>
<div data-role="wda_asandakov_hlprop_filter_value_wrapper">
	<div data-role="wda_asandakov_hlprop_filter_value_inputs" data-name="<?=$inputName;?>">
		<?foreach($mValue as $value):?>
			<input type="hidden" name="<?=$inputName;?>" value="<?=htmlspecialcharsbx($value['VALUE']);?>" />
		<?endforeach?>
	</div>
	<?
	_ShowUserPropertyField('PROP['.$arProperty['ID'].']', $arProperty, $mValue, true, false, 50000, '', false);
	?>
</div>
<script>
if(!window.wdaSetValuesAsandakovHlpropFilterInterval){
	window.wdaSetValuesAsandakovHlpropFilterInterval = setInterval(function(){
		const separator = '<?=$separator;?>';
		const div = $('div[data-role="wda_asandakov_hlprop_filter_value_wrapper"]');
		const inputs = $('div[data-role="wda_asandakov_hlprop_filter_value_inputs"]').html('');
		const name = inputs.attr('data-name')
		$('tr:has(input[type="text"])', div).each(function(){
			const prop = $(this).children('td').children('input[type="text"]').first();
			const value = prop.val().trim();
			if(value != '' && !$(`input[value="${value}"]`, inputs).length){
				inputs.append($(`<input type="hidden" name="${name}" value="${value}" />`))
			}
		});
	}, 500);
}
</script>
