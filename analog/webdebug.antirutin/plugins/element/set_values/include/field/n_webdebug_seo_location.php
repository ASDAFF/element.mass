<?
namespace WD\Antirutin;
$bAllowMultiply = false;
$arProperty = &$arField['DATA'];
$mValue = is_array($mValue) ? $mValue : [$mValue];
$mValue = array_map(function($item){
	return ['VALUE' => $item, 'DESCRIPTION' => ''];
}, $arValues);
$arProperty['VALUE'] = $mValue;
$arProperty['~VALUE'] = $mValue;

ob_start();
_ShowUserPropertyField('PROP['.$arProperty['ID'].']', $arProperty, $mValue, true, false, 50000, '', false);
$strHtml = ob_get_clean();
$strHtml = preg_replace('#<select\s#i', '<select data-no-style="Y" ', $strHtml);
$strHtml = preg_replace('#name="PROP\[\d+\](\[\d+\])?(\[VALUE\])?(\[\])?"#', 'name="'.$this->getInputName('value').'$3"', $strHtml);
print $strHtml;
