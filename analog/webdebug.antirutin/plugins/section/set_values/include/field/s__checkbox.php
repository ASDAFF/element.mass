<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

$arValues = [
	'Y' => Helper::getMessage('MAIN_YES'),
	'N' => Helper::getMessage('MAIN_NO'),
];
$arValues = [
	'REFERENCE_ID' => array_keys($arValues),
	'REFERENCE' => array_values($arValues),
];
print selectBoxFromArray($this->getInputName('value', $bMultiple), $arValues, $mValue);
?>
