<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'NAME'] = 'Произвольный PHP-код';
	$MESS[$strLang.'HINT'] = 'Произвольный PHP-код.';

$MESS[$strLang.'DEMO_CODE'] = '$this->debug(\'Hello world!\');
$this->debug(\'Section ID: \'.$intSectionId);
$this->debug(\'IBlock ID: \'.$this->intIBlockId);
# Get element data:
$arSelect = [
	\'NAME\',
	\'CODE\',
	\'UF_TEXT_1\', # OR: \'UF_*\',
	\'SEO\' => true,
];
$arSection = IBlock::getSectionArray($intSectionId, $this->intIBlockId, $arSelect);
$this->debug($arSection);
return true;';

$MESS[$strLang.'ERROR_NO_CODE'] = 'Пустое поле с PHP-кодом.';
?>