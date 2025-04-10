<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'MODE'] = 'Что обрабатывать';
	$MESS[$strHint.'MODE'] = 'Выберите, что модулю необходимо обработать: только товар, либо только его предложения, либо и то и другое.';
	$MESS[$strLang.'MODE_PRODUCT'] = 'Товар';
	$MESS[$strLang.'MODE_OFFERS'] = 'Торговые предложения товара';
	$MESS[$strLang.'MODE_ALL'] = 'Товар и его торговые предложения';

$MESS[$strLang.'STORES'] = 'Выберите склады';
	$MESS[$strHint.'STORES'] = 'Выберите склады, по которым должен считаться общий остаток.';

$strSaveToPropName = 'Сохранить не в поле «Доступное количество», а в отдельное свойство';
$strSaveToPropHint = 'Опция позволяет сохранить результат расчёта остатка не в поле «Доступное количество», а в выбранное свойство товара.';
$MESS[$strLang.'PRODUCT_SAVE_TO_PROPERTY'] = 'Для товаров: '.$strSaveToPropName;
	$MESS[$strHint.'PRODUCT_SAVE_TO_PROPERTY'] = $strSaveToPropHint;
$MESS[$strLang.'OFFER_SAVE_TO_PROPERTY'] = 'Для предложений: '.$strSaveToPropName;
	$MESS[$strHint.'OFFER_SAVE_TO_PROPERTY'] = $strSaveToPropHint;

$MESS[$strLang.'SAVE_OFFERS_SUMM_TO_PROPERTY'] = 'Для предложений: не сохранять данные, но записать общий результат в товар';
	$MESS[$strHint.'SAVE_OFFERS_SUMM_TO_PROPERTY'] = 'Данная опция позволяет просчитать остатки предложений по заданным складам и сохранить полученное значение в свойство товара.';

$MESS[$strLang.'ERROR_NO_STORES'] = 'Не выбраны склады для пересчета остатка.';
?>