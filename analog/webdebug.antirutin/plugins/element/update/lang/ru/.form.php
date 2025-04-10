<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'CATALOG_PRODUCT_UPDATE'] = 'Обновить товар торгового каталога [CCatalogProduct::Update]';
	$MESS[$strHint.'CATALOG_PRODUCT_UPDATE'] = 'Выполнить CcatalogProduct::Update, чтобы сработал обработчик <b><code>OnProductUpdate</code></b>.';
$MESS[$strLang.'IBLOCK_ELEMENT_UPDATE'] = 'Обновить элемент инфоблока [CIBlockElement::Update]';
	$MESS[$strHint.'IBLOCK_ELEMENT_UPDATE'] = 'Выполнить CIBlockElement::Update, чтобы сработали обработчики <b><code>OnBeforeIBlockElementUpdate</code></b>, <b><code>OnAfterIBlockElementUpdate</code></b>.';
$MESS[$strLang.'IBLOCK_ELEMENT_UPDATE_WORKFLOW'] = 'Изменение в режиме документооборота';
	$MESS[$strHint.'IBLOCK_ELEMENT_UPDATE_WORKFLOW'] = 'Параметр управляет третьим аргументом в методе <a href="https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/update.php" target="_blank">CIBlockElement::update</a>.';
$MESS[$strLang.'IBLOCK_ELEMENT_UPDATE_UPDATESEARCH'] = 'Индексировать элемент для поиска';
	$MESS[$strHint.'IBLOCK_ELEMENT_UPDATE_UPDATESEARCH'] = 'Параметр управляет четвёртым аргументом в методе <a href="https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/update.php" target="_blank">CIBlockElement::update</a>.';
$MESS[$strLang.'IBLOCK_ELEMENT_UPDATE_RESIZEPICTURES'] = 'Использовать настройки инфоблока для обработки изображений';
	$MESS[$strHint.'IBLOCK_ELEMENT_UPDATE_RESIZEPICTURES'] = 'Параметр управляет пятым аргументом в методе <a href="https://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/update.php" target="_blank">CIBlockElement::update</a>.';
$MESS[$strLang.'IBLOCK_ELEMENT_UPDATE_WITH_FIELDS'] = 'С полным набором данных (экспериментальная опция!)';
	$MESS[$strHint.'IBLOCK_ELEMENT_UPDATE_WITH_FIELDS'] = 'Отметьте галочку, если элементы нужно пересохранять со всеми имеющимися данными. В противном случае элементы сохраняются только с указанием даты обновления.<br/><br/>
<b>Внимание!</b> Это экспериментальная опция. В некоторых случаях может привести к частичной потере данных товара. Набор передаваемых данных может отличаться от набора данных, который передается при сохранении на странице редактирования товара.';

?>