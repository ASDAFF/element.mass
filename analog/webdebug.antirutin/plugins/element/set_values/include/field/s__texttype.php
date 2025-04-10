<?
namespace WD\Antirutin;

$arValues = [
	'text' => static::getMessage('DESCRIPTION_TYPE_TEXT'),
	'html' => static::getMessage('DESCRIPTION_TYPE_HTML'),
];
$arValues = [
	'REFERENCE_ID' => array_keys($arValues),
	'REFERENCE' => array_values($arValues),
];
print selectBoxFromArray($this->getInputName('value', $bMultiple), $arValues, $mValue);
?>
