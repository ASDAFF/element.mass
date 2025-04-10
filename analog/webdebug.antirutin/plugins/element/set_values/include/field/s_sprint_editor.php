<?
namespace WD\Antirutin;
$arProperty = &$arField['DATA'];
if(is_null($mValue)){
	$mValue = html_entity_decode($arProperty['DEFAULT_VALUE']);
}
$mValue = is_array($mValue) ? $mValue : [$mValue];
$arProperty['VALUE'] = $mValue;
$arProperty['~VALUE'] = $mValue;
?>
<div data-role="wda_sprint_editor_filter_value_wrapper">
	<input type="hidden" name="<?=$this->getInputName('value');?>" value="<?=htmlspecialcharsbx($mValue[0]);?>"
		data-role="wda_sprint_editor_filter_value_hidden" />
	<form id="wda_form_sprint_editor">
		<?
		ob_start();
		_ShowUserPropertyField('PROP['.$arProperty['ID'].']', $arProperty, $mValue, true, false, 50000, '', false);
		$strHtml = preg_replace('#<tr>#', 
			'<tr data-role="wda_sprint_editor_filter_value" id="tr_PROPERTY_'.$arProperty['ID'].'">', ob_get_clean(), 1);
		print $strHtml;
		?>
	</form>
</div>
<script>
$('div[data-role="wda_sprint_editor_filter_value_wrapper"]')
	.closest('table[data-role="field_items"]').css('width', '100%');
$(document).on('submit', 'form#wda_form_sprint_editor', function(e){
	e.preventDefault();
});
if(!window.wdaSetValuesSprintEditorInterval){
	window.wdaSetValuesSprintEditorInterval = setInterval(function(){
		$('form#wda_form_sprint_editor').submit();
		$('tr[data-role="wda_sprint_editor_filter_value"]').each(function(){
			var div = $(this).closest('div[data-role="wda_sprint_editor_filter_value_wrapper"]'),
				inputWd = $('input[data-role="wda_sprint_editor_filter_value_hidden"]', div);
				inputSprintEditor = $(this).children('td').children('textarea.sp-x-result<?=$arProperty['ID'];?>').first(),
				inputSprintEditorValue = inputSprintEditor.val();
			if(inputWd.val() != inputSprintEditorValue) {
				inputWd.val(inputSprintEditorValue);
			}
		});
	}, 1000);
}
</script>
