<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

$arValues = [
	'D' => Helper::getMessage('WDA_D'),
	'Y' => Helper::getMessage('WDA_Y'),
	'N' => Helper::getMessage('WDA_N'),
];
$arValues = [
	'REFERENCE_ID' => array_keys($arValues),
	'REFERENCE' => array_values($arValues),
];
print selectBoxFromArray($this->getInputName('value', $bMultiple), $arValues, $mValue);
?>
