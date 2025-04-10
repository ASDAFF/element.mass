<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'IBLOCK_SECTION_UPDATE'] = 'Обновить раздел инфоблока [CIBlockSection::Update]';
	$MESS[$strHint.'IBLOCK_SECTION_UPDATE'] = 'Выполнить CIBlockSection::Update, чтобы сработали обработчики <b><code>OnBeforeIBlockSectionUpdate</code></b>, <b><code>OnAfterIBlockSectionUpdate</code></b>.';
$MESS[$strLang.'IBLOCK_SECTION_UPDATE_RESORT'] = 'Пересчёт границ разделов';
	$MESS[$strHint.'IBLOCK_SECTION_UPDATE_RESORT'] = 'Параметр управляет третьим аргументом в методе <a href="https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblocksection/update.php" target="_blank">CIBlockElement::update</a>.';
$MESS[$strLang.'IBLOCK_SECTION_UPDATE_UPDATESEARCH'] = 'Индексировать раздел для поиска';
	$MESS[$strHint.'IBLOCK_SECTION_UPDATE_UPDATESEARCH'] = 'Параметр управляет четвёртым аргументом в методе <a href="https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblocksection/update.php" target="_blank">CIBlockElement::update</a>.';
$MESS[$strLang.'IBLOCK_SECTION_UPDATE_RESIZEPICTURES'] = 'Использовать настройки инфоблока для обработки изображений';
	$MESS[$strHint.'IBLOCK_SECTION_UPDATE_RESIZEPICTURES'] = 'Параметр управляет пятым аргументом в методе <a href="https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblocksection/update.php" target="_blank">CIBlockElement::update</a>.';
$MESS[$strLang.'IBLOCK_SECTION_UPDATE_WITH_FIELDS'] = 'С полным набором данных (экспериментальная опция!)';
	$MESS[$strHint.'IBLOCK_SECTION_UPDATE_WITH_FIELDS'] = 'Отметьте галочку, если разделы нужно пересохранять со всеми имеющимися данными. В противном случае разделы сохраняются только с указанием даты обновления.<br/><br/>
<b>Внимание!</b> Это экспериментальная опция. В некоторых случаях может привести к частичной потере данных раздела. Набор передаваемых данных может отличаться от набора данных, который передается при сохранении на странице редактирования раздела.';

?>