<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'TYPE'] = 'Тип товара';
	$MESS[$strHint.'TYPE'] = 'Выберите тип товара: набор, комплект, или авторежим (в этом случае будет взята наибольшая сумма).';
	$MESS[$strLang.'TYPE_AUTO'] = 'авто';
	$MESS[$strLang.'TYPE_SET'] = 'набор';
	$MESS[$strLang.'TYPE_GROUP'] = 'комплект';

$MESS[$strLang.'PRICE_TYPE'] = 'Тип цен';
	$MESS[$strHint.'PRICE_TYPE'] = 'Выберите типы цен, для которого будет произведён подсчёт.';

$MESS[$strLang.'CURRENCY_ID'] = 'Валюта';
	$MESS[$strHint.'CURRENCY_ID'] = 'Выберите валюту, в которой будет сохранена цена.';

$MESS[$strLang.'ZERO_PRICE'] = 'Очищать цену комплекта/набора, если хотя бы у одного товара из его состава не указана цена';
	$MESS[$strHint.'ZERO_PRICE'] = 'Данная опция может помочь в случае, когда для одного или нескольких товаров из состава комплекта/набора не указана цена (например, если товар под заказ) - в этом случае лучше обнулить цену, чем установить её неправильно.';
?>