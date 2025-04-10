<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'NAME'] = 'Произвольный PHP-код';
	$MESS[$strLang.'HINT'] = 'Произвольный PHP-код.';

$MESS[$strLang.'DEMO_CODE'] = '$this->debug(\'Hello world!\');
$this->debug(\'Element ID: \'.$intElementId);
$this->debug(\'IBlock ID: \'.$this->intIBlockId);
# Get element data:
$arSelect = [
	\'FIELDS\' => [\'NAME\', \'CODE\'], # OR: \'FIELDS\' => true,
	#\'PROPERTY_ID\' => [123, 124, 125], # OR: \'PROPERTY_ID\' => true,
	#\'EMPTY_PROPERTIES\' => true,
	#\'CATALOG\' => true,
	#\'PRICES\' => [1, 2, 3],
	#\'STORES\' => true,
	#\'SEO\' => true,
];
$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arSelect);
$this->update($intElementId, [\'EXTERNAL_ID\' => \'TEST\']);
$this->setPropertyValue($intElementId, $intPropertyId = 34, \'TEST1\');
return true;';

$MESS[$strLang.'ERROR_NO_CODE'] = 'Пустое поле с PHP-кодом.';
?>