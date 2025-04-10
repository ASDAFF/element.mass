<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

$arValues = [
	'1' => Helper::getMessage('WDA_Y'),
	'0' => Helper::getMessage('WDA_N'),
];
$arValues = [
	'REFERENCE_ID' => array_keys($arValues),
	'REFERENCE' => array_values($arValues),
];
print selectBoxFromArray($this->getInputName('value', $bMultiple), $arValues, $mValue);
?>
